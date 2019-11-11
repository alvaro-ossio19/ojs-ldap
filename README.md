# OJS LDAP plugin para UPCH

> Plugin para fuente de autenticación en OJS utilizando LDAP de UPCH.

## Instalación

Agregar plugin como submodulo en OJS en el archivo .gitmodules y agregar al final:

    [submodule "plugins/auth/ldap-upch"]
	path = plugins/auth/ldap-upch
	url = https://github.com/alvaro-ossio19/ojs-ldap.git

Luego ejecutar en terminal:

    $ git submodule update --init --recursive

Finalmente, registrar el plugin como fuente de autenticación en la base de datos de OJS:

    INSERT INTO auth_sources(auth_id,title,plugin,auth_default) VALUES(1,'LDAP UPCH','ldap-upch',1);