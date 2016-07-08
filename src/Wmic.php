<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Cityware\Wmi;

/**
 * Description of Wmic
 *
 * @author fsvxavier
 */
class Wmic implements WmiInterface {

    /**
     * The host of the current WMI connection.
     *
     * @var string
     */
    private $host;

    /**
     * The domain of the current WMI connection.
     *
     * @var string
     */
    private $domain;

    /**
     * The username of the user connecting to the host.
     *
     * @var string
     */
    private $username;

    /**
     * The password of the user connecting to the host.
     *
     * @var string
     */
    private $password;

    /**
     * Constructor.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $domain
     */
    public function __construct($host = 'localhost', $username = null, $password = null, $domain = null) {
        $this->setHost($host);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setDomain($domain);
    }

    /**
     * Returns a new connection to the
     * server using the current WMIC Linux.
     *
     * @param string $namespace
     *
     * @return bool|Connection
     */
    public function connect($namespace = 'root\cimv2') {
        
        $namespacePrepared = escapeshellarg($namespace);

        $connection = "wmic -U " . $this->getDomain() . $this->getUsername() . " --password=" . $this->password . " //" . $this->getHost() . " --namespace=" . $namespacePrepared . " ";

        if ($connection) {


            // Set the connection
            $this->setConnection(new Connection($connection));

            return $this->connection;
        }

        return false;
    }

    public function getConnection(){
        
    }
    
    /**
     * Sets the current connection.
     *
     * @param ConnectionInterface $connection
     *
     * @return $this
     */
    public function setConnection(ConnectionInterface $connection) {
        $this->connection = $connection;

        return $this;
    }

    /**
     * Returns the current host to connect to.
     *
     * @return string
     */
    public function getHost() {
        return $this->host;
    }

    /**
     * Returns the current domain to connect to.
     *
     * @return string
     */
    public function getDomain() {
        return (!empty($this->domain)) ? $this->domain . "\\" : null;
    }

    /**
     * Returns the current username.
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     * Sets the host to connect to.
     *
     * @param string $host
     *
     * @return $this
     */
    public function setHost($host) {
        $this->host = (string) $host;

        return $this;
    }

    /**
     * Sets the host to connect to.
     *
     * @param string $domain
     *
     * @return $this
     */
    public function setDomain($domain) {
        $this->domain = (string) $domain;

        return $this;
    }

    /**
     * Sets the current username.
     *
     * @param string $username
     *
     * @return $this
     */
    public function setUsername($username) {
        $this->username = (string) $username;

        return $this;
    }

    /**
     * Sets the current password.
     *
     * @param string $password
     *
     * @return $this
     */
    public function setPassword($password) {
        $this->password = (string) $password;

        return $this;
    }

}
