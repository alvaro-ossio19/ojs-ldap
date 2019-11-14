# OJS LDAP plugin para UPCH

> Plugin para fuente de autenticación en OJS utilizando LDAP de UPCH.

## Instalación (solo una vez)

Ejecutar en terminal en la carpeta de OJS:

    $ git submodule add https://github.com/alvaro-ossio19/ojs-ldap.git  plugins/auth/ldap-upch

Esto habrá agregado al plugin como submodulo en OJS en el archivo .gitmodules:

    [submodule "plugins/auth/ldap-upch"]
	path = plugins/auth/ldap-upch
	url = https://github.com/alvaro-ossio19/ojs-ldap.git

## Actualización

Para actualizar los submodulos, ejecutar en terminal:

    $ git submodule update --init --recursive

## Registrar fuente de autenticación

Finalmente, registrar el plugin como fuente de autenticación en la base de datos de OJS:

    INSERT INTO auth_sources(auth_id,title,plugin,auth_default) VALUES(1,'LDAP UPCH','ldap-upch',1);

## Plugin DEV

Para la creación y actualización de plugins de PKP, leer la [Guía de Desarrollo de Plugins](https://docs.pkp.sfu.ca/dev/plugin-guide/en/settings).