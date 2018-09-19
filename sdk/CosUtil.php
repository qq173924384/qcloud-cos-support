<?php
/**
 *
 */
class CosUtil
{
    public static function getClient()
    {
        $cos_options = get_option('cos_options', true);
        return new Qcloud\Cos\Client([
            'region'      => esc_attr($cos_options['region']),
            'credentials' => [
                'appId'     => esc_attr($cos_options['app_id']),
                'secretId'  => esc_attr($cos_options['secret_id']),
                'secretKey' => esc_attr($cos_options['secret_key']),
            ],
        ]);
    }
    public static function upload($file, $name)
    {
        $cos_options = get_option('cos_options', true);

        $client = CosUtil::getClient();
        return $client->Upload(esc_attr($cos_options['bucket']), $name, fopen($file, 'rb'));
    }
    public function delete($name)
    {
        $cos_options = get_option('cos_options', true);

        $client = CosUtil::getClient();
        return $client->deleteObject([
            'Bucket' => esc_attr($cos_options['bucket']) . '-' . esc_attr($cos_options['app_id']),
            'Key'    => $name,
        ]);
    }
    public static function getObjectUrl($name)
    {
        return self::getUrl($name);
    }
    public static function getDownloadUrl($url)
    {
        $url = ltrim(ltrim($url, 'http:'), '//');
        $url = substr($url, strpos($url, '/') + 1);
        return self::getUrl($url);
    }
    public static function getUrl($key)
    {
        $client = CosUtil::getClient();
        $url    = $client->getObjectUrl(esc_attr($cos_options['bucket']), $key, '+10 minutes');
        $url    = explode('?', $url);
        $url[0] = urldecode($url[0]);
        return implode('?', $url);
    }
}
