<?php

class Currency
{
    static $currencies = array();
    static $currency = 'RUB';

    /**
     * This method converts input $value amount into $toCurrency currency, Example : 100, 'RUB'
     * @param integer $value
     * @param String $toCurrency
     */
    public static function convert($value, $toCurrency = 'RUB')
    {
        if ($toCurrency == 'EUR')
            return ceil($value);
        if (!self::$currencies[$toCurrency])
        {
            $path = 'data/currencies/';
            if (!file_exists($path . $toCurrency) || filemtime($path . $toCurrency) < time() - 60 * 60 * 24)
            {
                $toParse = file_get_contents('http://www.ecb.int/euro.html');
                preg_match('/\('.$toCurrency.'\)\s*=\s*(.*?)<br/si', $toParse, $found);
                self::$currencies[$toCurrency] = floatval(str_replace(',', '.', $found[1]));
                file_put_contents($path . $toCurrency, self::$currencies[$toCurrency]);
                chmod($path . $toCurrency, 0777);
            }
            else
                self::$currencies[$toCurrency] = floatval(file_get_contents($path . $toCurrency));
        }
        return ceil($value * self::$currencies[$toCurrency]);
    }


    public static function format($value)
    {
        $value = self::convert($value, self::$currency);
        return number_format($value).' '.self::$currency;
    }

}
