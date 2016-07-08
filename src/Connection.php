<?php

namespace Cityware\Wmi;

use Cityware\Wmi\Processors\HardDisks;
use Cityware\Wmi\Processors\Processors;
use Cityware\Wmi\Processors\Registry;
use Cityware\Wmi\Processors\Software;
use Cityware\Wmi\Query\Builder;

class Connection implements ConnectionInterface {

    /**
     * The current connection.
     *
     * @var mixed
     */
    private $connection;

    /**
     * The current connection.
     *
     * @var mixed
     */
    private $connectionString;

    /**
     * Constructor.
     *
     * @param mixed $connection
     */
    public function __construct($connection) {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            $this->connection = $connection;
        } else {
            $this->connectionString = $connection;
        }
    }

    /**
     * Returns the current raw COM connection.
     *
     * @return mixed
     */
    public function get() {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->connection;
        } else {
            return $this->connectionString;
        }
    }

    /**
     * Returns a new Registry processor instance.
     *
     * @return Registry
     */
    public function registry() {
        return new Registry($this);
    }

    /**
     * Returns a new Software processor instance.
     *
     * @return Software
     */
    public function software() {
        return new Software($this);
    }

    /**
     * Returns a new Processors processor instance.
     *
     * @return Processors
     */
    public function processors() {
        return new Processors($this);
    }

    /**
     * Returns a new HardDisks processor instance.
     *
     * @return HardDisks
     */
    public function hardDisks() {
        return new HardDisks($this);
    }

    /**
     * Executes the specified query on the current connection.
     *
     * @param string $query
     *
     * @return mixed
     */
    public function query($query) {

        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->connection->ExecQuery($query);
        } else {
            $execString = $this->connectionString . '"' . $query . '"';
            $aReturn = Array();
            $inc = $wmiout = $execstatus = null;
            exec($execString, $wmiout, $execstatus);
            $wmiCount = count($wmiout);

            if ($wmiCount > 0) {
                $names = explode('|', $wmiout[1]); // build the names list to dymanically output it
                for ($i = 2; $i < $wmiCount; $i++) { // dynamically output the key:value pairs to suit cacti
                    $data = explode('|', $wmiout[$i]);
                    $j = 0;
                    $inc = $i - 2;
                    $aReturn[$inc] = new \stdClass();
                    foreach ($data as $item) {
                        $jIndex = $j++;
                        if (isset($names[$jIndex])) {
                            $objName = $names[$jIndex];

                            $aReplace = array(':', '_', '  ', '   ', '    ', '     ', '      ', '       ', '        ', '         ', '          ', '           ', '            ');
                            $aReturn[$inc]->$objName = str_replace($aReplace, ' ', $item);
                        } else {
                            $j = $jIndex - 1;
                        }
                    }
                }
            }
            $stdObjReturn = (object) $aReturn;

            return $stdObjReturn;
        }
    }

    /**
     * Returns a new query builder instance.
     *
     * @return Builder
     */
    public function newQuery() {
        return new Builder($this);
    }

}
