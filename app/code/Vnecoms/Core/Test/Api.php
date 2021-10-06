<?php

namespace Vnecoms\Core\Test;

use Zend\Http\Client;
use Zend\Http\Request;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Config\ConfigOptionsListConstants;

class Api
{
    const VNECOMS_URL = 'https://www.vnecoms.com/license/info';
    const ENCODED_KEY = '3132cf3739a48ae62cabbc56b5e899f0';

    /**
     * @var \Magento\Framework\Filesystem
     */
    protected $fileSystem;
    
    /**
     * @var \Magento\Framework\App\DeploymentConfig
     */
    protected $deploymentConfig;
    
    /**
     * @param \Magento\Framework\Filesystem $fileSystem
     * @param \Magento\Framework\App\DeploymentConfig $deploymentConfig
     */
    public function __construct(
        \Magento\Framework\Filesystem $fileSystem,
        \Magento\Framework\App\DeploymentConfig $deploymentConfig
    ){
        $this->fileSystem = $fileSystem;
        $this->deploymentConfig = $deploymentConfig;
    }
    
    /**
     * Decode an encoded string.
     *
     * @param string $encoded
     * @param string $key
     *
     * @return string
     */
    private function decode($encoded)
    {
        $key = self::ENCODED_KEY;

        return base64_decode(str_replace($key, '', $encoded));
        /* 
        $code = trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($encoded), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
        return $code; */
    }

    /**
     * Encode.
     *
     * @param string $code
     * @param string $key
     *
     * @return string
     */
    private function encode($code)
    {
        $key = self::ENCODED_KEY;

        return $key.base64_encode($code);

        /* $code = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $code, MCRYPT_MODE_CBC, md5(md5($key))));
        return $code; */
    }

    /**
     * Get key info from remote server.
     *
     * @param string $licenseKey
     *
     * @return Ambigous <\Zend\Http\Response, string, \Zend\Http\Response\Stream, unknown, \Zend\Http\Response\Stream>
     */
    public function getKeyInfo($licenseKey)
    {
        $client = new Client(self::VNECOMS_URL.'/index', [
            'maxredirects' => 0,
            'timeout' => 120,
        ]);

        $client->setMethod(Request::METHOD_POST);
        $licenseKey = $this->encode($licenseKey);

        $client->setParameterPost([
            'license_key' => $licenseKey,
            'plaintext' => 1,
        ]);
        $response = $client->send();
        try {
            $response = unserialize($this->decode($response->getBody()));
        } catch (\Exception $e) {
            throw new LocalizedException(__('[102] Cannot retrieve license information this time. Please try again later'));
        }
        if (!is_array($response) || !isset($response['success'])) {
            throw new LocalizedException(__('Cannot retrieve license information.'));
        }
        if (!$response['success']) {
            throw new LocalizedException(__($response['msg']));
        }

        return $response['result'];
    }

    /**
     * get File system.
     *
     * @return \Magento\Framework\Filesystem
     */
    private function getFileSystem()
    {
        return $this->fileSystem;
    }

    /**
     * Get extensions list from remote server.
     *
     * @return Ambigous <\Zend\Http\Response, string, \Zend\Http\Response\Stream, unknown, \Zend\Http\Response\Stream>
     */
    public function getExtensionsList()
    {
        $filePath = $this->getFileSystem()->getDirectorywrite(DirectoryList::MEDIA)->getAbsolutePath('/catalog/product/');
        $filename = 'data.txt';
        $dataFile = $filePath.$filename;
        $reupdate = false;
        $validator = new \Zend_Validate_File_Exists();
        $validator->addDirectory($filePath);
        $fileExist = $validator->isValid($filename);
        if ($validator->isValid($filename)) {
            $reupdate = time() > (filemtime($filePath.$filename) + 604800);
        }
        if (!$fileExist || $reupdate) {
            /*Update extension list*/
            $client = new Client(self::VNECOMS_URL.'/extensions', [
                'maxredirects' => 0,
                'timeout' => 120,
            ]);

            $client->setMethod(Request::METHOD_GET);
            $client->setParameterGet([
                'plaintext' => 1,
            ]);
            $response = $client->send();
            $response = $response->getBody();

            /*Try to decode the extension list*/
            try {
                $extensionsArr = unserialize($this->decode($response));
                if (!is_array($extensionsArr)) {
                    throw new LocalizedException(__('[100] Cannot retrieve license information this time. Please try again later'));
                }

                /*Save the extensin list*/
                if (!$validator->isValid('')) {
                    mkdir($filePath, 0777, true);
                } /*If the folder does not exist just create it.*/
                if (!file_put_contents($dataFile, $response)) {
                    throw new LocalizedException(__('[101] Folder permission error. Make sure these folders and their subfolders have write permissions: pub/static, pub/media'));
                }
            } catch (LocalizedException $e) {
                throw new LocalizedException(__($e->getMessage()));
            } catch (\Exception $e) {
                throw new LocalizedException(__('[102] Cannot retrieve license information this time. Please try again later'));
            }
        }

        /*Get extensions list from saved data*/
        try {
            $extensionLists = file_get_contents($dataFile);
            $extensionLists = unserialize($this->decode($extensionLists));
            if (!is_array($extensionLists)) {
                throw new LocalizedException(__('[103] Cannot retrieve license information this time. Please try again later'));
            }
            $result = [];
            foreach ($extensionLists as $extension) {
                $result[$extension['extension_name']] = $extension;
            }

            return $result;
        } catch (LocalizedException $e) {
            throw new LocalizedException(__($e->getMessage()));
        } catch (\Exception $e) {
            throw new LocalizedException(__('[104] Cannot retrieve license information this time. Please try again later'));
        }

        return [];
    }

    /**
     * Save the license key from remote server (vnecoms.com).
     *
     * @param string $licenseKey
     * @param array  $domains
     *
     * @throws LocalizedException
     *
     * @return mixed
     */
    public function remoteSaveLicenseKey($licenseKey, $domains = [])
    {
        $client = new Client(self::VNECOMS_URL.'/save', [
            'maxredirects' => 0,
            'timeout' => 120,
        ]);

        $client->setMethod(Request::METHOD_POST);

        $domains = serialize($domains);
        $client->setParameterPost([
            'license_key' => $this->encode($licenseKey),
            'secure_key' => $this->encode($this->getSecureKey()),
            'domains' => $this->encode($domains),
            'plaintext' => 1,
        ]);
        $response = $client->send();
        try {
            $response = unserialize($this->decode($response->getBody()));
        } catch (\Exception $e) {
            throw new LocalizedException(__('[105] Cannot save license information this time. Please try again later'));
        }
        if (!is_array($response) || !isset($response['success'])) {
            throw new LocalizedException(__('Cannot save license information.'));
        }
        if (!$response['success']) {
            throw new LocalizedException(__($response['msg']));
        }

        return $response['result'];
    }

    /**
     * Get secure key.
     *
     * @return string
     */
    public function getSecureKey()
    {
        $configs = [
            ConfigOptionsListConstants::CONFIG_PATH_CRYPT_KEY,
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT.'/host',
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT.'/dbname',
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT.'/username',
            ConfigOptionsListConstants::CONFIG_PATH_DB_CONNECTION_DEFAULT.'/password',
        ];
        $key = [];

        foreach ($configs as $config) {
            $key[] = $this->deploymentConfig->get($config);
        }

        $key = md5(implode('-', $key));

        return wordwrap($key, 4, '-', true);
    }

    /**
     * Decode the saved key.
     * 
     * @param \Vnecoms\Core\Model\Key $key
     *
     * @return bool|Ambigous <boolean, mixed>
     */
    public function getSavedKeyInfo(\Vnecoms\Core\Model\Key $key)
    {
        if (!$key->getLicenseInfo()) {
            return false;
        }
        try {
            $result = unserialize($this->decode($key->getLicenseInfo(), self::ENCODED_KEY));
        } catch (\Exception $e) {
            $result = false;
        }

        return $result;
    }

    public function renderLicenseInfo(\Vnecoms\Core\Model\Key $key)
    {
        $currentServerDomain = $_SERVER['HTTP_HOST'];

        $licenseInfo = $key->getSavedKeyInfo();
        $defaultSecureKey = $key->getSecureKey();

        if (!$licenseInfo) {
            return '<div style="color: #E22626;">'.__('License information is not available.').'</div>';
        }
        $result = '';
        $domains = isset($licenseInfo['domains']) ? $licenseInfo['domains'] : [];
        /*Show error if the current domain is not in the list*/
        if (!in_array($currentServerDomain, $domains)) {
            $result .= '<div style="color: #E22626;margin-bottom: 15px;">'.__('Your current domain %1 is not registered with this license.', '<strong>'.$currentServerDomain.'</strong>').'</div>';
        }
        $result .= '<label style="width:150px;float: left;">'.__('Extension:').'</label><strong>'.$licenseInfo['item_name'].'</strong><br />';
        $result .= '<label style="width:150px;float: left;">'.__('License Type:').'</label>'.$licenseInfo['type'].'<br />';
        $result .= '<label style="width:150px;float: left;">'.__('Registered Secure Key:').'</label>'.$licenseInfo['secure_key'].'<br />';
        $result .= ($defaultSecureKey == $licenseInfo['secure_key'])?'<br />':
            '<label style="width:150px;float: left;">'.__('Current Secure Key:').'</label><strong style="font-weight: bold; color: #eb5202">'.$defaultSecureKey.'</strong><br />'
            .'<span class="admin__field-error" style="max-width: 600px;">'.__('Your secure key is different with the registered secure key. The related extension(s) will not be acitvated.<br />Please contact us if you want to change your secure key').'</span><br /><br />';
        
        $isFirstRow = true;
        foreach ($domains as $domain) {
            $result .= '<label style="width:150px;float: left;">'.($isFirstRow?'Domain: ':'&nbsp;').'</label>'.$domain.'<br />';
            if ($isFirstRow) {
                $isFirstRow = false;
            }
        }

        $result .= '<br />';
        $extensionPackages = isset($licenseInfo['licensed_extensions']) ? $licenseInfo['licensed_extensions'] : [];
        $isFirstRow = true;
        foreach ($extensionPackages as $package) {
            $result .= '<label style="width:150px;float: left;">'.($isFirstRow?'Packages: ':'&nbsp;').'</label>'.$package.'<br />';
            if ($isFirstRow) {
                $isFirstRow = false;
            }
        }

        return $result;
    }
}
