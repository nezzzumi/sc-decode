<?php
function nm_unserialize_ini($ini_file)
{

    global $nm_config;

    $arr_prod_ini = getProdDefault();

    if (is_file($ini_file)) {
        $str_prod = file_get_contents($ini_file);

        if (substr($str_prod, 0, 8) == "<?php /*") {
            $str_prod = substr($str_prod, 8, -5);
        }

        $arr_prod_tmp = unserialize($str_prod);
        foreach ($arr_prod_tmp as $tag => $val) {
            $arr_prod_ini[$tag] = $val;
        }
    }

    return $arr_prod_ini;
}

function getProdDefault()
{
    $arr_prod_ini            = array();
    $arr_prod_ini["PROFILE"] = array();
    $arr_prod_ini["GLOBAL"]  = array();
    $arr_prod_ini["GLOBAL"]["GC_DIR"] = dirname(dirname(dirname(dirname(__FILE__)))) . '/tmp';
    $arr_prod_ini["GLOBAL"]["GC_MIN"] = '30';
    $arr_prod_ini["GLOBAL"]["PDF_SERVER"] = '';
    $arr_prod_ini["GLOBAL"]["JAVA_PATH"] = '';
    $arr_prod_ini["GLOBAL"]["JAVA_BIN"] = '';
    $arr_prod_ini["GLOBAL"]["JAVA_PROTOCOL"] = '';
    $arr_prod_ini["GLOBAL"]["SEC_TYPE"] = '';
    $arr_prod_ini["GLOBAL"]["SEC_HOST"] = '';
    $arr_prod_ini["GLOBAL"]["SEC_USER"] = '';
    $arr_prod_ini["GLOBAL"]["SEC_PASS"] = '';
    $arr_prod_ini["GLOBAL"]["SEC_BASE"] = '';
    $arr_prod_ini["GLOBAL"]["SEC_PATH"] = '';
    $arr_prod_ini["GLOBAL"]["GOOGLEMAPS_API_KEY"] = '';
    $arr_prod_ini["GLOBAL"]["SEC_USAR"] = "N";
    $arr_prod_ini["GLOBAL"]["PASSWORD"] = '';
    $arr_prod_ini["GLOBAL"]["LANGUAGE"] = '';

    return $arr_prod_ini;
}

function decode_string($string)
{
    $new_crypt = false;
    if (substr($string, 0, 13) == "enc_nm_enc_v1") {
        $string = substr($string, 13);
        $new_crypt = true;
    }

    $result = shift_letter($string, 9);
    $result = xoft_decode($result, "arroz com feijao");
    $result = substitution($result, "dec");
    $result = xoft_decode($result, "filet com fritas");
    $result = shift_letter($result, 21);

    if ($new_crypt) {
        $result = unicode_decode64($result);
    }

    return ($result);
}
function shift_letter($plain, $shift)
{
    $cipher = "";
    for ($i = 0; $i < strlen($plain); $i++) {
        $p = substr($plain, $i, 1);
        $p = ord($p);
        if (($p >= 97) && ($p <= 122)) {
            $c = $p + $shift;
            if ($c > 122) {
                $c = $c - 26;
            }
        } elseif (($p >= 65) && ($p <= 90)) {
            $c = $p + $shift;
            if ($c > 90) {
                $c = $c - 26;
            }
        } else {
            $c = $p;
        }
        $c      = chr($c);
        $cipher = $cipher . $c;
    }
    return ($cipher);
}

function xoft_decode($cipher_data, $key)
{
    $m             = 0;
    $all_bin_chars = "";
    for ($i = 0; $i < strlen($cipher_data); $i++) {
        $c             = substr($cipher_data, $i, 1);
        $decimal_value = base64todec($c);
        $decimal_value = ($decimal_value - $m) / 4;
        $four_bit      = decbin($decimal_value);
        while (strlen($four_bit) < 4) {
            $four_bit = "0" . $four_bit;
        }
        $all_bin_chars = $all_bin_chars . $four_bit;
        $m++;
        if ($m > 3) {
            $m = 0;
        }
    }
    $key_length = 0;
    $plain_data = "";
    for ($j = 0; $j < strlen($all_bin_chars); $j = $j + 8) {
        $c         = substr($all_bin_chars, $j, 8);
        $k         = substr($key, $key_length, 1);
        $dec_chars = bindec($c);
        $dec_chars = $dec_chars - strlen($key);
        $c         = chr($dec_chars);
        $key_length++;
        if ($key_length >= strlen($key)) {
            $key_length = 0;
        }
        $dec_chars  = ord($c) ^ ord($k);
        $p          = chr($dec_chars);
        $plain_data = $plain_data . $p;
    }
    return ($plain_data);
}

function substitution($plain, $operation)
{
    $decode  = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ=";
    $encode  = "blPqoVjBiOrnDucdxLyICaRSfAkFQsNtmzZKMpHeTGWhUXJwvYgE_";
    $d_temp  = string_to_array($decode);
    $e_temp  = string_to_array($encode);
    $array_d = array();
    $array_e = array();
    for ($i = 0; $i < sizeof($d_temp); $i++) {
        $array_d[$e_temp[$i]] = $d_temp[$i];
        $array_e[$d_temp[$i]] = $e_temp[$i];
    }
    $cipher = "";
    $array  = ("dec" == $operation) ? $array_d : $array_e;
    for ($i = 0; $i < strlen($plain); $i++) {
        $char    = substr($plain, $i, 1);
        $cipher .= (in_array($char, $array)) ? $array[$char] : $char;
    }
    return ($cipher);
}

function unicode_decode64($v_str_string)
{
    return base64_decode($v_str_string);
}

function string_to_array($string)
{
    $str_chunk = chunk_split($string, 1, " ");
    $array     = explode(" ", substr($str_chunk, 0, strlen($str_chunk) - 1));
    return ($array);
}

function base64todec($base64_value)
{
    switch ($base64_value) {
        case "A":
            $decimal_value = 0;
            break;
        case "B":
            $decimal_value = 1;
            break;
        case "C":
            $decimal_value = 2;
            break;
        case "D":
            $decimal_value = 3;
            break;
        case "E":
            $decimal_value = 4;
            break;
        case "F":
            $decimal_value = 5;
            break;
        case "G":
            $decimal_value = 6;
            break;
        case "H":
            $decimal_value = 7;
            break;
        case "I":
            $decimal_value = 8;
            break;
        case "J":
            $decimal_value = 9;
            break;
        case "K":
            $decimal_value = 10;
            break;
        case "L":
            $decimal_value = 11;
            break;
        case "M":
            $decimal_value = 12;
            break;
        case "N":
            $decimal_value = 13;
            break;
        case "O":
            $decimal_value = 14;
            break;
        case "P":
            $decimal_value = 15;
            break;
        case "Q":
            $decimal_value = 16;
            break;
        case "R":
            $decimal_value = 17;
            break;
        case "S":
            $decimal_value = 18;
            break;
        case "T":
            $decimal_value = 19;
            break;
        case "U":
            $decimal_value = 20;
            break;
        case "V":
            $decimal_value = 21;
            break;
        case "W":
            $decimal_value = 22;
            break;
        case "X":
            $decimal_value = 23;
            break;
        case "Y":
            $decimal_value = 24;
            break;
        case "Z":
            $decimal_value = 25;
            break;
        case "a":
            $decimal_value = 26;
            break;
        case "b":
            $decimal_value = 27;
            break;
        case "c":
            $decimal_value = 28;
            break;
        case "d":
            $decimal_value = 29;
            break;
        case "e":
            $decimal_value = 30;
            break;
        case "f":
            $decimal_value = 31;
            break;
        case "g":
            $decimal_value = 32;
            break;
        case "h":
            $decimal_value = 33;
            break;
        case "i":
            $decimal_value = 34;
            break;
        case "j":
            $decimal_value = 35;
            break;
        case "k":
            $decimal_value = 36;
            break;
        case "l":
            $decimal_value = 37;
            break;
        case "m":
            $decimal_value = 38;
            break;
        case "n":
            $decimal_value = 39;
            break;
        case "o":
            $decimal_value = 40;
            break;
        case "p":
            $decimal_value = 41;
            break;
        case "q":
            $decimal_value = 42;
            break;
        case "r":
            $decimal_value = 43;
            break;
        case "s":
            $decimal_value = 44;
            break;
        case "t":
            $decimal_value = 45;
            break;
        case "u":
            $decimal_value = 46;
            break;
        case "v":
            $decimal_value = 47;
            break;
        case "w":
            $decimal_value = 48;
            break;
        case "x":
            $decimal_value = 49;
            break;
        case "y":
            $decimal_value = 50;
            break;
        case "z":
            $decimal_value = 51;
            break;
        case "0":
            $decimal_value = 52;
            break;
        case "1":
            $decimal_value = 53;
            break;
        case "2":
            $decimal_value = 54;
            break;
        case "3":
            $decimal_value = 55;
            break;
        case "4":
            $decimal_value = 56;
            break;
        case "5":
            $decimal_value = 57;
            break;
        case "6":
            $decimal_value = 58;
            break;
        case "7":
            $decimal_value = 59;
            break;
        case "8":
            $decimal_value = 60;
            break;
        case "9":
            $decimal_value = 61;
            break;
        case "+":
            $decimal_value = 62;
            break;
        case "/":
            $decimal_value = 63;
            break;
        case "=":
            $decimal_value = 64;
            break;
        default:
            $decimal_value = 0;
            break;
    }
    return ($decimal_value);
}


$config = nm_unserialize_ini("config.php");

foreach ($config['PROFILE'] as $profile) {
    echo decode_string($profile["VAL_HOST"]) . PHP_EOL;
    echo decode_string($profile["VAL_USER"]) . PHP_EOL;
    echo decode_string($profile["VAL_PASS"]) . PHP_EOL;
    echo decode_string($profile["VAL_BASE"]) . PHP_EOL . PHP_EOL;
}
