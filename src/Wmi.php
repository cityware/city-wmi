<?php

namespace Cityware\Wmi;

use COM;

class Wmi implements WmiInterface {

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
     * The current COM instance.
     *
     * @var COM
     */
    private $com;

    /**
     * The current COM connection to the host.
     *
     * @var Connection
     */
    private $connection;

    /**
     * The WMI COM script.
     *
     * @var string
     */
    private $script = 'WbemScripting.SWbemLocator';

    /**
     * Constructor.
     *
     * @param string $host
     * @param string $username
     * @param string $password
     * @param string $domain
     */
    public function __construct($host = 'localhost', $username = null, $password = null, $domain = null) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->com = new COM($this->script);
        }

        $this->setHost($host);
        $this->setUsername($username);
        $this->setPassword($password);
        $this->setDomain($domain);
    }

    /**
     * Returns a new connection to the
     * server using the current COM instance.
     *
     * @param string $namespace
     * @param int    $level
     *
     * @return bool|Connection
     */
    public function connect($namespace = '', $level = 3) {


        // Connect to the host using the specified namespace
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $connection = $this->com->ConnectServer($this->getHost(), $namespace, $this->getDomain() . $this->getUsername(), $this->password);
        } else {
            $namespacePrepared = escapeshellarg($namespace);
            $connection = "wmic -U " . $this->getDomain() . $this->getUsername() . " --password=" . $this->password . " //" . $this->getHost() . " --namespace=" . $namespacePrepared . " ";
        }

        if ($connection) {
            if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
                // Set the impersonation level
                $connection->Security_->ImpersonationLevel = (int) $level;
            }

            // Set the connection
            $this->setConnection(new Connection($connection));

            return $this->connection;
        }

        return false;
    }

    /**
     * Returns the current connection to the host.
     *
     * @return bool|Connection
     */
    public function getConnection() {
        if ($this->connection instanceof ConnectionInterface) {
            return $this->connection;
        }

        return false;
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
        return (!empty($this->domain)) ? $this->domain . "/" : null;
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
