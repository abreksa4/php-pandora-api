<?php
/**
 * Basic Pandora API access class.
 *
 * @author Alex Dallaway <github:adallaway>
 * @see http://pan-do-ra-api.wikia.com/
 */

namespace php_pandora_api;

use GuzzleHttp\Client;

class Pandora
{

    CONST accessory_accessoryConnect = 'accessory.accessoryConnect';
    CONST ad_getAdMetadata = 'ad.getAdMetadata';
    CONST ad_registerAd = 'ad.registerAd';
    CONST auth_getAdMetadata = 'auth.getAdMetadata';
    CONST auth_partnerAdminLogin = 'auth.partnerAdminLogin';
    CONST auth_partnerLogin = 'auth.partnerLogin';
    CONST auth_userLogin = 'auth.userLogin';
    CONST bookmark_addArtistBookmark = 'bookmark.addArtistBookmark';
    CONST bookmark_addSongBookmark = 'bookmark.addSongBookmark';
    CONST bookmark_deleteArtistBookmark = 'bookmark.deleteArtistBookmark';
    CONST bookmark_deleteSongBookmark = 'bookmark.deleteSongBookmark';
    CONST device_associateDeviceForCasting = 'device.associateDeviceForCasting';
    CONST device_createDevice = 'device.createDevice';
    CONST device_disassociateCastingDevice = 'device.disassociateCastingDevice';
    CONST device_disassociateDevice = 'device.disassociateDevice';
    CONST music_getSearchRecommendations = 'music.getSearchRecommendations';
    CONST music_getTrack = 'music.getTrack';
    CONST music_publishSongShare = 'music.publishSongShare';
    CONST music_search = 'music.search';
    CONST music_shareMusic = 'music.shareMusic';
    CONST station_addFeedback = 'station.addFeedback';
    CONST station_addMusic = 'station.addMusic';
    CONST station_createStation = 'station.createStation';
    CONST station_deleteFeedback = 'station.deleteFeedback';
    CONST station_deleteMusic = 'station.deleteMusic';
    CONST station_deleteStation = 'station.deleteStation';
    CONST station_getGenreStationsChecksum = 'station.getGenreStationsChecksum';
    CONST station_getGenreStations = 'station.getGenreStations';
    CONST station_getPlaylist = 'station.getPlaylist';
    CONST station_getStation = 'station.getStation';
    CONST station_publishStationShare = 'station.publishStationShare';
    CONST station_renameStation = 'station.renameStation';
    CONST station_shareStation = 'station.shareStation';
    CONST station_transformSharedStation = 'station.transformSharedStation';
    CONST test_checkLicensing = 'test.checkLicensing';
    CONST test_echo = 'test.echo';
    CONST track_explainTrack = 'track.explainTrack';
    CONST track_trackStarted = 'track.trackStarted';
    CONST user_accountMessageDismissed = 'user.accountMessageDismissed';
    CONST user_acknowledgeSubscriptionExpiration = 'user.acknowledgeSubscriptionExpiration';
    CONST user_associateDevice = 'user.associateDevice';
    CONST user_authorizeFacebook = 'user.authorizeFacebook';
    CONST user_canSubscribe = 'user.canSubscribe';
    CONST user_changeSettings = 'user.changeSettings';
    CONST user_createUser = 'user.createUser';
    CONST user_disconnectFacebook = 'user.disconnectFacebook';
    CONST user_emailPassword = 'user.emailPassword';
    CONST user_facebookAuthFailed = 'user.facebookAuthFailed';
    CONST user_getBookmarks = 'user.getBookmarks';
    CONST user_getFacebookInfo = 'user.getFacebookInfo';
    CONST user_getSettings = 'user.getSettings';
    CONST user_getStationListChecksum = 'user.getStationListChecksum';
    CONST user_getStationList = 'user.getStationList';
    CONST user_getUsageInfo = 'user.getUsageInfo';
    CONST user_purchaseAmazonPayToPlay = 'user.purchaseAmazonPayToPlay';
    CONST user_purchaseAmazonSubscription = 'user.purchaseAmazonSubscription';
    CONST user_purchaseGooglePayToPlay = 'user.purchaseGooglePayToPlay';
    CONST user_purchaseGoogleSubscription = 'user.purchaseGoogleSubscription';
    CONST user_purchaseItunesSubscription = 'user.purchaseItunesSubscription';
    CONST user_setAwareOfProfile = 'user.setAwareOfProfile';
    CONST user_setExplicitContentFilter = 'user.setExplicitContentFilter';
    CONST user_setQuickMix = 'user.setQuickMix';
    CONST user_sleepSong = 'user.sleepSong';
    CONST user_startComplimentaryTrial = 'user.startComplimentaryTrial';

    public $last_error;
    public $last_request_data;
    public $last_response_data;
    protected $_username;
    protected $_password;
    protected $_partner_username;
    protected $_partner_password;
    protected $_device_model;
    protected $_encryption_cipher;
    protected $_decryption_cipher;
    protected $_endpoint_version;
    protected $_json_endpoint;
    protected $_endpoint_mode;
    protected $_current_params;
    protected $_last_synctime;
    protected $_guzzle_client;

    public function __construct($partner_username = 'android')
    {
        $this->_current_params = array();

        $available_partners = array
        (
            // tuner.pandora.com
            'android' => array('AC7IBG09A3DTSYM4R41UJWL07VLN8JI7', 'android-generic', 'R=U!LH$O2B#', '6#26FRL$ZWD', false),
            'iphone' => array('P2E4FC0EAD3*878N92B2CDp34I0B1@388137C', 'IP01', '20zE1E47BE57$51', '721^26xE22776', false),
            'palm' => array('IUC7IBG09A3JTSYM4N11UJWL07VLH8JP0', 'pre', 'E#U$MY$O2B=', '%526CBL$ZU3', false),
            'winmo' => array('ED227E10a628EB0E8Pm825Dw7114AC39', 'VERIZON_MOTOQ9C', '7D671jt0C5E5d251', 'v93C8C2s12E0EBD', false),

            // internal-tuner.pandora.com
            'pandora one' => array('TVCKIBGS9AO9TSYLNNFUML0743LH82D', 'D01', 'U#IO$RZPAB%VX2', '2%3WCL*JU$MP]4', true),
            'windowsgadget' => array('EVCCIBGS9AOJTSYMNNFUML07VLH8JYP0', 'WG01', 'E#IO$MYZOAB%FVR2', '%22CML*ZU$8YXP[1', true),
        );

        if (!isset($available_partners[$partner_username])) {
            $partner_username = 'android';
        }

        $this->_partner_username = $partner_username;
        $this->_endpoint_mode = 'json';

        list
            (
            $this->_partner_password,
            $this->_device_model,
            $this->_decryption_cipher,
            $this->_encryption_cipher,
            $use_internal_tuner
            ) = $available_partners[$partner_username];

        $endpoint_host = ($use_internal_tuner) ? 'internal-tuner.pandora.com' : 'tuner.pandora.com';
        $json_endpoint_base = '/services/json/';

        $this->_json_endpoint = "//{$endpoint_host}{$json_endpoint_base}";

        $this->_endpoint_version = '5';
        $this->_guzzle_client = new Client();
    }

    public function login($username = null, $password = null)
    {
        $this->_current_params = array();
        if (!is_null($username)) {
            $this->_username = $username;
        }
        if (!is_null($password)) {
            $this->_password = $password;
        }

        $partner_login_response = $this->sendRequest(
            'auth.partnerLogin',
            array
            (
                'username' => $this->_partner_username,
                'password' => $this->_partner_password,
                'deviceModel' => $this->_device_model,
                'version' => $this->_endpoint_version
            ),
            false,
            true
        );

        if ($partner_login_response === false) {
            return false;
        }

        $this->_current_params['auth_token'] = $partner_login_response['partnerAuthToken'];
        $this->_current_params['partner_id'] = $partner_login_response['partnerId'];

        $user_login_response = $this->sendRequest(
            'auth.userLogin',
            array
            (
                'loginType' => 'user',
                'username' => $this->_username,
                'password' => $this->_password,
                'partnerAuthToken' => $this->_current_params['auth_token'],
                'syncTime' => $this->_last_synctime
            ),
            true
        );

        if ($user_login_response === false) {
            return false;
        }

        $this->_current_params['user_id'] = $user_login_response['userId'];
        $this->_current_params['auth_token'] = $user_login_response['userAuthToken'];

        return true;
    }

    protected function sendRequest($method, $data, $encrypted = false, $ssl = false)
    {
        return $this->sendJsonRequest($method, $data, $encrypted, $ssl);
    }

    protected function sendJsonRequest($method, $data, $encrypted = false, $ssl = false)
    {
        $protocol = (!$ssl) ? 'http:' : 'https:';

        $url_params = $this->_current_params + array('method' => $method);
        $url = ($protocol . $this->_json_endpoint . '?' . http_build_query($url_params));

        $json_data = json_encode($data);
        $this->last_request_data = $json_data;

        if ($encrypted) {
            $json_data = bin2hex(mcrypt_encrypt(MCRYPT_BLOWFISH, $this->_encryption_cipher, $json_data, MCRYPT_MODE_ECB));
        }

        $response = $this->_guzzle_client->post($url, [
            'body' => $json_data,
            'headers' => [
                'Content-Type' => 'text/plain'
            ],
            'verify' => false
        ]);


        $this->last_response_data = $response->getBody()->getContents();
        $response = json_decode($this->last_response_data, true);

        if (!isset($response['stat']) || $response['stat'] != 'ok') {
            $this->last_error = $response['message'] . ' (' . $this->defineErrorCode($response['code']) . ')';

            return false;
        }

        if (isset($response['result']['syncTime'])) {
            $this->_last_synctime = $this->decryptSyncTime($response['result']['syncTime']);
        }

        return $response['result'];
    }

    protected function defineErrorCode($error_code)
    {
        $error_codes = array
        (
            '0' => 'INTERNAL',
            '1' => 'MAINTENANCE_MODE',
            '2' => 'URL_PARAM_MISSING_METHOD',
            '3' => 'URL_PARAM_MISSING_AUTH_TOKEN',
            '4' => 'URL_PARAM_MISSING_PARTNER_ID',
            '5' => 'URL_PARAM_MISSING_USER_ID',
            '6' => 'SECURE_PROTOCOL_REQUIRED',
            '7' => 'CERTIFICATE_REQUIRED',
            '8' => 'PARAMETER_TYPE_MISMATCH',
            '9' => 'PARAMETER_MISSING',
            '10' => 'PARAMETER_VALUE_INVALID',
            '11' => 'API_VERSION_NOT_SUPPORTED',
            '12' => 'LICENSING_RESTRICTIONS',
            '13' => 'INSUFFICIENT_CONNECTIVITY',
            '14' => 'UNKNOWN_METHOD_NAME?',
            '15' => 'WRONG_PROTOCOL?',
            '1000' => 'READ_ONLY_MODE',
            '1001' => 'INVALID_AUTH_TOKEN',
            '1002' => 'INVALID_PARTNER_LOGIN',
            '1003' => 'LISTENER_NOT_AUTHORIZED',
            '1004' => 'USER_NOT_AUTHORIZED',
            '1005' => 'MAX_STATIONS_REACHED',
            '1006' => 'STATION_DOES_NOT_EXIST',
            '1007' => 'COMPLIMENTARY_PERIOD_ALREADY_IN_USE',
            '1008' => 'CALL_NOT_ALLOWED',
            '1009' => 'DEVICE_NOT_FOUND',
            '1010' => 'PARTNER_NOT_AUTHORIZED',
            '1011' => 'INVALID_USERNAME',
            '1012' => 'INVALID_PASSWORD',
            '1013' => 'USERNAME_ALREADY_EXISTS',
            '1014' => 'DEVICE_ALREADY_ASSOCIATED_TO_ACCOUNT',
            '1015' => 'UPGRADE_DEVICE_MODEL_INVALID',
            '1018' => 'EXPLICIT_PIN_INCORRECT',
            '1020' => 'EXPLICIT_PIN_MALFORMED',
            '1023' => 'DEVICE_MODEL_INVALID',
            '1024' => 'ZIP_CODE_INVALID',
            '1025' => 'BIRTH_YEAR_INVALID',
            '1026' => 'BIRTH_YEAR_TOO_YOUNG',
            '1027' => 'INVALID_COUNTRY_CODE',
            '1027' => 'INVALID_GENDER',
            '1034' => 'DEVICE_DISABLED',
            '1035' => 'DAILY_TRIAL_LIMIT_REACHED',
            '1036' => 'INVALID_SPONSOR',
            '1037' => 'USER_ALREADY_USED_TRIAL'
        );

        return isset($error_codes[$error_code]) ? $error_codes[$error_code] : 'UNKNOWN_ERROR';
    }

    protected function decryptSyncTime($sync_time_encypted)
    {
        $sync_time_encypted = hex2bin($sync_time_encypted);
        $sync_time_decypted = mcrypt_decrypt(MCRYPT_BLOWFISH, $this->_decryption_cipher, $sync_time_encypted, MCRYPT_MODE_ECB);

        return intval(substr($sync_time_decypted, 4));
    }

    /**
     * Send a request off to Pandora and hope for the best :)
     *
     * | Methods                                                    | Params
     * | -------------------------------- | ----------------------------------------
     * | bookmark.addArtistBookmark       | (string) trackToken
     * | bookmark.addSongBookmark         | (string) trackToken
     * | music.search                     | (string) searchText
     * | station.addFeedback              | (string) trackToken, (boolean) isPositive
     * | station.addMusic                 | (string) musicToken, (string) stationToken
     * | station.createStation            | (string) musicToken, (string) trackToken, (string) musicType['song'|'artist']
     * | station.deleteFeedback           | (string) feedbackId
     * | station.deleteMusic              | (string) seedId
     * | station.deleteStation            | (string) stationToken
     * | station.getGenreStations         | -none-
     * | station.getGenreStationsChecksum | -none-
     * | station.getPlaylist              | (string) stationToken, (string) additionalAudioUrl['HTTP_*_*','...']
     * | ...                              |     @see http://pan-do-ra-api.wikia.com/wiki/Json/5/station.getPlaylist
     * | station.getStation               | (string) stationToken, (boolean) includeExtendedAttributes
     * | station.shareStation             | (string) stationId, (string) stationToken, (array) emails
     * | station.renameStation            | (string) stationToken, (string) stationName
     * | station.transformSharedStation   | (string) stationToken
     * | track.explainTrack               | (string) trackToken
     * | user.canSubscribe                | -none-
     * | user.createUser                  | (string) accountType, (string) countryCode, (string) registeredType['user']
     * | ...                              |     (string) username, (string) password, (int) birthYear, (string) zipCode
     * | ...                              |     (string) gender['male'|'female'], (boolean) emailOptIn
     * | user.getBookmarks                |
     * | user.getStationList              | (boolean) includeStationArtUrl
     * | user.getStationListChecksum      | -none-
     * | user.setQuickMix                 | (array) quickMixStationIds
     * | user.sleepSong                   | (string) trackToken
     *
     * For a full, up-to-date listing see https://6xq.net/pandora-apidoc/json/
     *
     * @param string $method
     * @param array $params
     * @return bool|array
     */
    public function makeRequest($method, $params = array())
    {
        $response = $this->sendRequest(
            $method,
            $params + array
            (
                'userAuthToken' => $this->_current_params['auth_token'],
                'syncTime' => $this->_last_synctime
            ),
            true,
            false
        );

        if ($response === false) {
            return false;
        }

        return $response;
    }
}
