<?php
/*
=============================================
 Name      : Downloads v1.8
 Author    : Mehmet Hanoğlu ( MaRZoCHi )
 Site      : https://mehmethanoglu.com.tr
 License   : MIT License
=============================================
*/

if ( ! defined( 'DATALIFEENGINE' ) ) {
	die( "Hacking attempt!" );
}

global $config, $dset;

include ENGINE_DIR . "/data/download.conf.php";

if ( ( ! empty( $area ) && $dset['use_static'] ) || ( empty( $area ) && $dset['use_news'] ) ) {

	function replace_url( $url ) {
		global $config;
		if ( $config['allow_alt_url'] ) {
			$url = str_replace( "&amp;area=static", "/static", $url );
			return str_replace( "index.php?do=download&id=", "file/", $url );
		} else {
			return str_replace( "index.php?do=download", "index.php?do=downloads", $url );
		}
	}

	function external_url( $url ) {
		return str_replace( "a href", "a target=\"_blank\" rel=\"external\" href", $url );
	}

	function _replace_hashlink( $m ) {
		global $config, $dset;
		if ( $config['allow_alt_url'] ) {
			return "href=\"" . $m[1] . "/file/" . md5( $dset['hash_key'] . $m[2] ) . "\"";
		} else {
			return "href=\"" . $m[1] . "index.php?do=downloads&amp;id=" . md5( $dset['hash_key'] . $m[2] ) . "\"";
		}
	}

	function replace_hashlink( $url ) {
		return preg_replace_callback( "#href=\"(.*?)index\.php\?do=download\&amp;id=([0-9]+)\"#", "_replace_hashlink", $url );
	}

	function replace_namelink( $url ) {
		global $config, $dset, $file_names;
		// print_r( $file_names );
		// echo $url . " -- ";

		// yapılacak - start
		if ( $config['allow_alt_url'] ) {
			if ( strpos( $url, "area=static" ) === false ) {
				preg_match( "#id=([0-9]+)#is", $url, $m );
				return str_replace( "index.php?do=download&id=" . $m[1], "file/" . $file_names[ $m[1] ], $url );
			}
		} else {
			return str_replace( "index.php?do=download", "index.php?do=downloads", $url );
		}
		// yapılacak - end

	}

	if ( $dset['use_linkas'] == "0" ) {
		$replace_1 = array_map( "replace_url", $replace_1 );
		$replace_2 = array_map( "replace_url", $replace_2 );
	}

	else if ( $dset['use_linkas'] == "1" ) {
		$replace_1 = array_map( "replace_hashlink", $replace_1 );
		$replace_2 = array_map( "replace_hashlink", $replace_2 );
	}

	else if ( $dset['use_linkas'] == "2" && count( $file_names ) > 0 ) {
		$replace_1 = array_map( "replace_namelink", $replace_1 );
		$replace_2 = array_map( "replace_namelink", $replace_2 );
	}

	if ( $dset['open_ext'] ) {
		$replace_1 = array_map( "external_url", $replace_1 );
		$replace_2 = array_map( "external_url", $replace_2 );
	}
}
