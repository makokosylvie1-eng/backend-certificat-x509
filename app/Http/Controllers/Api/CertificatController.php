<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CertificatController extends Controller
{
    /**
     * Numéros de série (hexadécimal, sans ":") considérés comme révoqués dans la démo.
     * Ajoute ici le numéro de série d'un certificat de test pour montrer un cas "révoqué".
     */
    private const DEMO_REVOKED_SERIALS = [];

    public function parseCertificate(Request $request): JsonResponse
    {
        $request->validate([
            'certificate' => ['required', 'string', 'max:20000'],
        ]);

        $pem = trim($request->input('certificate'));

        $cert   = str_contains($pem, '-----BEGIN CERTIFICATE-----') ? @openssl_x509_read($pem) : false;
        $parsed = $cert ? openssl_x509_parse($cert) : false;

        // Vide la file d'erreurs OpenSSL pour ne pas polluer les requêtes suivantes.
        while (openssl_error_string() !== false) {
        }

        if (! $cert || ! $parsed) {
            return response()->json([
                'success' => false,
                'message' => "Certificat illisible : le texte n'est pas un certificat X.509 PEM valide.",
            ], 422);
        }

        // --- Empreintes ---
        $format = fn ($hex) => $hex ? implode(':', str_split(strtoupper($hex), 2)) : 'N/A';
        $sha256 = $format(openssl_x509_fingerprint($cert, 'sha256'));
        $sha1   = $format(openssl_x509_fingerprint($cert, 'sha1'));

        // --- Identités ---
        $subjectStr = $this->dn($parsed['subject'] ?? []);
        $issuerStr  = $this->dn($parsed['issuer'] ?? []);
        $selfSigned = ($parsed['subject'] ?? []) == ($parsed['issuer'] ?? []);

        // --- Période de validité ---
        $now       = time();
        $notBefore = $parsed['validFrom_time_t'] ?? 0;
        $notAfter  = $parsed['validTo_time_t'] ?? 0;

        if ($now < $notBefore) {
            $validityStatus = 'Pas encore valide';
        } elseif ($now > $notAfter) {
            $validityStatus = 'Expiré depuis ' . (int) floor(($now - $notAfter) / 86400) . ' jour(s)';
        } else {
            $validityStatus = 'Valide, expire dans ' . (int) floor(($notAfter - $now) / 86400) . ' jour(s)';
        }

        // --- Clé publique et signature ---
        $details = [];
        if ($pub = openssl_pkey_get_public($cert)) {
            $details = openssl_pkey_get_details($pub) ?: [];
        }
        $bits    = $details['bits'] ?? null;
        $keyType = match ($details['type'] ?? null) {
            OPENSSL_KEYTYPE_RSA => 'RSA',
            OPENSSL_KEYTYPE_EC  => 'EC',
            OPENSSL_KEYTYPE_DSA => 'DSA',
            default             => 'Inconnu',
        };
        $sigAlgo = $parsed['signatureTypeSN'] ?? ($parsed['signatureTypeLN'] ?? 'Inconnu');

        // --- Numéro de série ---
        $serialHex = strtoupper($parsed['serialNumberHex'] ?? '');
        $serial    = $serialHex !== '' ? implode(':', str_split($serialHex, 2)) : 'N/A';

        // --- CRL / OCSP (simulés, d'après les extensions réellement présentes) ---
        $extensions = $parsed['extensions'] ?? [];
        $revoked    = in_array($serialHex, self::DEMO_REVOKED_SERIALS, true);

        if ($revoked) {
            $crlStatus  = 'Révoqué (simulé : numéro de série présent dans la liste de démonstration)';
            $ocspStatus = 'Réponse OCSP : Revoked (simulé)';
        } else {
            $crlStatus = isset($extensions['crlDistributionPoints'])
                ? 'Point de distribution CRL présent. Vérification simulée : non révoqué'
                : 'Aucun point de distribution CRL dans le certificat : vérification impossible';

            $ocspStatus = str_contains($extensions['authorityInfoAccess'] ?? '', 'OCSP')
                ? 'Répondeur OCSP déclaré. Vérification simulée : Good'
                : 'Aucun répondeur OCSP déclaré dans le certificat : vérification impossible';
        }

        // --- Diagnostic TLS : constats réels sur le certificat ---
        $diagnostics = [];
        if ($now > $notAfter) {
            $diagnostics[] = 'Certificat expiré : un navigateur le refuserait';
        } elseif ($now < $notBefore) {
            $diagnostics[] = "Certificat pas encore valide (date de début dans le futur)";
        }
        if ($selfSigned) {
            $diagnostics[] = 'Auto-signé : aucune autorité de confiance ne le garantit (erreur de chaîne de confiance)';
        }
        if (preg_match('/md5|sha1/i', $sigAlgo)) {
            $diagnostics[] = "Algorithme de signature faible ($sigAlgo)";
        }
        if ($keyType === 'RSA' && $bits !== null && $bits < 2048) {
            $diagnostics[] = "Clé RSA trop courte ($bits bits, 2048 minimum recommandé)";
        }
        if (empty($diagnostics)) {
            $diagnostics[] = 'Aucun problème détecté sur ce certificat';
        }

        return response()->json([
            'success' => true,
            'data' => [
                'subject'              => $subjectStr,
                'issuer'               => $issuerStr,
                'validFrom'            => gmdate('Y-m-d H:i:s', $notBefore) . ' UTC',
                'validTo'              => gmdate('Y-m-d H:i:s', $notAfter) . ' UTC',
                'validity_status'      => $validityStatus,
                'serialNumber'         => $serial,
                'key_info'             => $bits ? "$keyType $bits bits" : $keyType,
                'signature_algorithm'  => $sigAlgo,
                'fingerprint_sha256'   => $sha256,
                'fingerprints'         => [
                    'sha256' => $sha256,
                    'sha1'   => $sha1,
                ],
                'crl_status'           => $crlStatus,
                'ocsp_status'          => $ocspStatus,
                'tls_diagnostic'       => implode(' ; ', $diagnostics),
            ],
        ]);
    }

    /** Transforme un nom distinctif OpenSSL en texte "CN=..., O=...". */
    private function dn(array $parts): string
    {
        return collect($parts)
            ->map(fn ($v, $k) => $k . '=' . (is_array($v) ? implode('/', $v) : $v))
            ->implode(', ');
    }
}