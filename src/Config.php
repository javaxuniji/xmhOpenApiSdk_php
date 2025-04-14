<?php

namespace XMH\OpenApiSdk;


use XMH\OpenApiSdk\Exception\SdkException;

class Config {
    private static string $signSalt;
    private static string $appId;
    private static string $appSecret;
    private static StorageInterface $storage;
    private static LoggerInterface $logger;
    private static string $env;
    private static string $domain;
    private static string $baseurl;

    /**
     * @throws SdkException
     */
    public static function getDomain(): string {
        switch (self::$env) {
            case    Common::ENV_ALPHA:
                return Common::DOMAIN_ALPHA;
            case Common::ENV_BETA:
                return Common::DOMAIN_BETA;
            case Common::ENV_IDC:
                return Common::DOMAIN_IDC;
            default:
                throw new SdkException('error env config', -1);
        }
    }

    public static function setDomain(string $domain) {
        self::$domain = $domain;

    }

    /**
     * @throws SdkException
     */
    public static function getBaseurl(): string {
        switch (self::$env) {
            case    Common::ENV_ALPHA:
                return Common::BASEURL_ALPHA;
            case Common::ENV_BETA:
                return Common::BASEURL_BETA;
            case Common::ENV_IDC:
                return Common::BASEURL_IDC;
            default:
                throw new SdkException('error env config', -1);
        }
    }

    public static function setBaseurl(string $baseurl) {
        self::$baseurl = $baseurl;
    }

    /**
     * @throws SdkException
     */
    public static function GetFullBaseUri():string {
        $domain = self::getDomain();
        $baseurl = self::getBaseurl();
        return "https://{$domain}/$baseurl";
    }

    public static function getEnv(): string {
        return self::$env;
    }

    public static function setEnv(string $env): void {
        self::$env = $env;
    }

    public static function getSignSalt(): string {
        return self::$signSalt;
    }

    public static function setSignSalt(string $signSalt): void {
        self::$signSalt = $signSalt;
    }

    public static function getAppId(): string {
        return self::$appId;
    }

    public static function setAppId(string $appId): void {
        self::$appId = $appId;
    }

    public static function getAppSecret(): string {
        return self::$appSecret;
    }

    public static function setAppSecret(string $appSecret): void {
        self::$appSecret = $appSecret;
    }

    public static function getStorage(): StorageInterface {
        return self::$storage === null ? new MemoryStorage() : self::$storage;
    }

    public static function setStorage(StorageInterface $storage): void {
        self::$storage = $storage;
    }

    public static function getLogger(): LoggerInterface {
        return self::$logger === null ? new DefaultLogger() : self::$logger;
    }

    public static function setLogger(LoggerInterface $logger): void {
        self::$logger = $logger;
    }

}