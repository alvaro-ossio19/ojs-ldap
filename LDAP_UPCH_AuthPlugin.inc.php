<?php

/**
 * @file plugins/auth/ldap/LDAP_UPCH_AuthPlugin.inc.php
 *
 * Copyright (c) 2014-2018 Simon Fraser University
 * Copyright (c) 2003-2018 John Willinsky
 * Distributed under the GNU GPL v2. For full terms see the file docs/COPYING.
 *
 * @class LDAP_UPCH_AuthPlugin
 * @ingroup plugins_auth_ldap
 *
 * @brief LDAP authentication plugin.
 */
import('lib.pkp.classes.plugins.AuthPlugin');

/**
 * [UPCH]
 * Este plugin ldap ha sido creado especificamente para esta version personalizada de OJS.
 * [/UPCH]
 */
class LDAP_UPCH_AuthPlugin extends AuthPlugin
{

  /**
   * Funcion necesaria para registrar nuevos usuarios fuera de la vista de creacion de usuarios en la revista.
   * Por ejm: en agregar revisor en la etapa de revision.
   *
   * @return integer
   */
    public static function getAuthId()
    {
        return 1;
    }

    /**
     * Called as a plugin is registered to the registry
     *
     * @param $category String Name of category plugin was registered to
     * @return boolean True iff plugin initialized successfully; if false,
     * the plugin will not be registered.
     */
    public function register($category, $path)
    {
        $success = parent::register($category, $path);
        $this->addLocaleData();
        return $success;
    }

    // LDAP-specific configuration settings:
    // - hostname
    // - port
    // - basedn
    // - managerdn
    // - managerpwd
    // - pwhash
    // - SASL: sasl, saslmech, saslrealm, saslauthzid, saslprop

    /** @var $conn resource the LDAP connection */
    public $conn;

    /**
     * Return the name of this plugin.
     * @return string
     */
    public function getName()
    {
        return 'ldap-upch';
    }

    /**
     * Return the localized name of this plugin.
     * @return string
     */
    public function getDisplayName()
    {
        return __('plugins.auth.ldap.displayName');
    }

    /**
     * Return the localized description of this plugin.
     * @return string
     */
    public function getDescription()
    {
        return __('plugins.auth.ldap.description');
    }

    //
    // Core Plugin Functions
    // (Must be implemented by every authentication plugin)

    //

    /**
   * Returns an instance of the authentication plugin
   * @param $settings array settings specific to this instance.
   * @param $authId int identifier for this instance
   * @return LDAPuthPlugin
   */
    public function getInstance($settings, $authId)
    {
        // reemplazar arreglo de configuracion del plugin para utilizarlo en todas las revistas.
        $settings['server']         = '172.17.100.5';
        $settings['domain']         = 'upch.edu.pe';
        $settings['dc']             = 'dc=upch,dc=edu,dc=pe';
        $settings['version']        = 3;
        $settings['port']           = 389;
        $settings['admin_username'] = 'Bot692651';
        $settings['admin_password'] = '691543gklbah';

        // crear la instancia personalizada del plugin
        $returner = new LDAP_UPCH_AuthPlugin($settings, $authId);
        return $returner;
    }

    /**
     * Authenticate a username and password.
     * @param $username string
     * @param $password string
     * @return boolean true if authentication is successful
     */
    public function authenticate($username, $password)
    {
        $valid = false;
        if ($password != null) {
            if ($this->open()) {
                $valid = $this->bind($username, $password);
                $this->close();
            }
        }
        return $valid;
    }

    /**
     * Check if a username exists.
     * @param $username string
     * @return boolean
     */
    public function userExists($username)
    {
        $exists = false;
        if ($this->open(true)) {
            if ($this->bind($this->settings['admin_username'], $this->settings['admin_password'])) {
                $filter = "(samaccountname={$username})";
                $result = ldap_search($this->conn, $this->settings['dc'], $filter);
                $exists = (ldap_count_entries($this->conn, $result) != 0);
            }
            $this->close();
        }
        return $exists;
    }

    //
    // LDAP Helper Functions
    //

    public function open($secure = false)
    {
        $prot       = ($secure) ? 'ldaps' : 'ldap';
        $this->conn = ldap_connect("{$prot}://{$this->settings['server']}/", $this->settings['port']);
        ldap_set_option($this->conn, LDAP_OPT_PROTOCOL_VERSION, $this->settings['version']);
        return $this->conn;
    }

    /**
     * Close connection.
     */
    public function close()
    {
        ldap_close($this->conn);
        $this->conn = null;
    }

    /**
     * Bind to a directory.
     * $binddn string directory to bind (optional)
     * $password string (optional)
     */
    public function bind($username, $password)
    {
        $binddn = $username . "@" . $this->settings['domain'];
        if (isset($this->settings['sasl'])) {
            // FIXME ldap_sasl_bind requires PHP5, haven't tested this
            return @ldap_sasl_bind($this->conn, $binddn, $password, $this->settings['saslmech'], $this->settings['saslrealm'], $this->settings['saslauthzid'], $this->settings['saslprop']);
        }
        return @ldap_bind($this->conn, $binddn, $password);
    }
}
