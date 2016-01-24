<?php

namespace Wassa\MPS;

/**
 * IosUtils
 */
class IosUtils
{
    public static function createTempCertFiles($certificate, $caCertificate = null)
    {
        $certificateResource = self::createTempFile($certificate);
        $caCertificateResource = $caCertificate ? IosUtils::createTempFile($caCertificate) : null;

        return array($certificateResource, $caCertificateResource);
    }

    public static function createTempFile($data) {
        $tmpFile = tmpfile();
        fwrite($tmpFile, base64_decode($data));
        fseek($tmpFile, 0);

        return $tmpFile;
    }

    public static function closeTempCertFiles($certificateTempResource, $caCertificateTempResource = null)
    {
        fclose($certificateTempResource);

        if ($caCertificateTempResource) {
            fclose($caCertificateTempResource);
        }

    }

    public static function getTempFileName($resource)
    {
        $metaData = stream_get_meta_data($resource);

        return $metaData['uri'];
    }
}
