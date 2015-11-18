<?php

namespace URLParser;

class URL
{
    /**
     * @var string
     */
    private $normalizePattern = '/\./i';

    /**
     * @var string
     */
    private $initUrl;

    /**
     * @var string
     */
    private $parsedUrl;

    /**
     * @var array
     */
    private $urlComponents;

    /**
     * @var array
     */
    private $secondLevelDomains = array();

    /**
     * URL constructor.
     * @param $url
     * @param string $protocol
     * @throws \Exception
     */
    public function __construct($url, $protocol = 'http')
    {
        if (empty($url)) {
            throw new \Exception('url parameter is required');
        } else {
            $this->initUrl = $url;
            $this->secondLevelDomains = require __DIR__ . '/domains/SecondLevelDomains.php';
            $this->parseUrl();
        }
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->parsedUrl;
    }

    /**
     * Parsing url
     */
    private function parseUrl()
    {
        if (preg_match('/\w:\/\//', $this->initUrl) !== 0) {
            $this->urlComponents = parse_url($this->initUrl);
        } else {
            $this->urlComponents = parse_url('justforparse://' . $this->initUrl);
            array_shift($this->urlComponents);
        }

        $this->parseDomain();
        $this->parsePath();
        $this->parseQuery();
    }

    private function parseDomain()
    {
        $domain = strtolower($this->urlComponents['host']);

        if (substr($domain, 0, 4) == 'www.') {
            $domain = substr($domain, 4);
        }

        if (filter_var($domain, FILTER_VALIDATE_IP)) {
            $this->urlComponents['host'] = array(
                'full' => $domain
            );
        } else {
            $domainArr = explode('.', $domain, count(explode('.', $domain)) - 1);

            //check for Second Level Domain
            $subDomains = $domainArr;
            $domainZone = array_pop($subDomains);

            if (in_array($domainZone, $this->secondLevelDomains)) {
                $mainDomain = array_pop($subDomains);

                $this->urlComponents['host'] = array(
                    'full' => $this->urlComponents['host'],
                    'sub-domains' => array(
                        'string' => implode('.', $subDomains),
                        'array' => $subDomains
                    ),
                    'original-domain' => $mainDomain . '.' . $domainZone,
                    'domain-zone' => $domainZone
                );
            } else {
                //check for Top Level Domain
                $mainDomain = array_pop($domainArr);

                $domainZone = explode('.', $mainDomain);
                array_shift($domainZone);

                $this->urlComponents['host'] = array(
                    'full' => $this->urlComponents['host'],
                    'sub-domains' => array(
                        'string' => implode('.', $domainArr),
                        'array' => $domainArr
                    ),
                    'original-domain' => $mainDomain,
                    'domain-zone' => implode('.', $domainZone)
                );
            }
        }
    }

    private function parsePath()
    {
        if (!empty($this->urlComponents['path'])) {
            $oldPath = $this->urlComponents['path'];
            $normalizedPath = preg_replace($this->normalizePattern, '', $this->urlComponents['path']);
            $tempPathStr = '';

            //remove slash at the beginning
            if (substr($normalizedPath, 0, 1) === '/') {
                $tempPathStr = substr($normalizedPath, 1);
            }

            //remove slash at the end
            if (substr($tempPathStr, -1) === '/') {
                $tempPathStr = substr($tempPathStr, 0, -1);
            }

            $this->urlComponents['path'] = array(
                'string' => $normalizedPath,
                'array' => explode('/', $tempPathStr)
            );

            $this->parsedUrl = str_replace($oldPath, $this->urlComponents['path']['string'], $this->initUrl);
        } else {
            $this->parsedUrl = $this->initUrl;
        }
    }

    private function parseQuery()
    {
        if (!empty($this->urlComponents['query'])) {
            parse_str($this->urlComponents['query'], $queryArr);

            $this->urlComponents['query'] = array(
                'string' => $this->urlComponents['query'],
                'array' => $queryArr
            );
        }
    }

    /**
     * Returns parsed URL components by key
     * @param string $key
     * @return string|array
     */
    private function getUrlComponent($key)
    {
        if (array_key_exists($key, $this->urlComponents)) {
            return $this->urlComponents[$key];
        } else {
            return '';
        }
    }

    /**
     * Returns initial url
     * @return string
     */
    public function getInitUrl()
    {
        return $this->initUrl;
    }

    /**
     * @return string
     */
    public function getParsedUrl()
    {
        return $this->parsedUrl;
    }

    /**
     * @return string
     */
    public function getHostName()
    {
        return $this->getUrlComponent('host')['full'];
    }

    /**
     * @return string
     */
    public function getSubDomains()
    {
        return $this->getUrlComponent('host')['sub-domains']['string'];
    }

    /**
     * @return string
     */
    public function getSubDomainsAsArray()
    {
        return $this->getUrlComponent('host')['sub-domains']['array'];
    }

    /**
     * @return string
     */
    public function getOriginalDomain()
    {
        return $this->getUrlComponent('host')['original-domain'];
    }

    /**
     * @return string
     */
    public function getDomainZone()
    {
        return $this->getUrlComponent('host')['domain-zone'];
    }

    /**
     * @return string
     */
    public function getProtocol()
    {
        return $this->getUrlComponent('scheme');
    }

    /**
     * @return string
     */
    public function getPort()
    {
        return $this->getUrlComponent('port');
    }

    /**
     * @return string
     */
    public function getUsername()
    {
        return $this->getUrlComponent('user');
    }

    /**
     * @return string
     */
    public function getPassword()
    {
        return $this->getUrlComponent('pass');
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->getUrlComponent('path')['string'];
    }

    /**
     * @return array
     */
    public function getPathAsArray()
    {
        return $this->getUrlComponent('path')['array'];
    }

    /**
     * @return string
     */
    public function getQuery()
    {
        return $this->getUrlComponent('query')['string'];
    }

    /**
     * @return array
     */
    public function getQueryAsArray()
    {
        return $this->getUrlComponent('query')['array'];
    }

    /**
     * @return string
     */
    public function getHash()
    {
        return $this->getUrlComponent('fragment');
    }
}
