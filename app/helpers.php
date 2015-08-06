<?php

/**
 * Outputs a time (in seconds) in a pretty format.
 *
 * @param $time
 *
 * @return string
 */
function display_seconds_pretty( $time, $ms = false )
{
    $time = intval( $time );
    $dtF = new DateTime("@0");
    $dtT = new DateTime("@$time");
    if ( $time > 31536000 ) {
        return $dtF->diff($dtT)->format('%y years');
    } elseif ( $time > 86400 ) {
        return $dtF->diff($dtT)->format('%ad %hh %im');
    } else {
        if ( $ms ) {
            return $dtF->diff($dtT)->format('%im %ss');
        }
        return $dtF->diff($dtT)->format('%hh %im');
    }
}

/**
 * Converts miles to kilometers.
 *
 * @param $distance
 *
 * @return mixed
 */
function mi_to_km( $distance )
{
	return $distance * 1.609344;
}

/**
 * Converts kilometers to miles.
 *
 * @param $distance
 *
 * @return float
 */
function km_to_mi( $distance )
{
	return $distance / 1.609344;
}

/**
 * @param $amount
 * @param string $singular
 * @param string $plural
 *
 * @return string
 */
function plural( $amount, $singular = '', $plural = 's' )
{
	return ( $amount == 1 ) ? $singular : $plural;
}

/**
 * Helper function to make a string 'key' ready for cache keys.
 * Probably a bit redundant but I like to party.
 *
 * @param $input
 *
 * @return mixed
 */
function keyify( $input )
{
	return str_replace( '.', '', $input );
}

/**
 * Helper function to make a string 'hypen/url' ready for URL params.
 * Probably a bit redundant but I really like to party.
 *
 * @param $input
 *
 * @return mixed
 */
function hyphenify( $input )
{
	return str_replace( ' ', '-', $input );
}

/**
 * Return calculated growth based on two numbers.
 *
 * @param $previous
 * @param $now
 * @param bool $display
 *
 * @return bool|float|string
 */
function calc_growth( $previous, $now, $display = false, $html = false )
{
	$change = ( $now > 0 && $previous > 0 ) ? ( ( $now - $previous ) / $previous ) * 100 : false;

	if ( ! $display )
	{
		return $change;
	}

	if ( $change > 0 )
	{
		return ( $html ) ? '<span class="growth positive">' . number_format($change, 2) . '%</span>' : number_format($change, 2) . '%';
	}
	else if ( $change < 0 )
	{
		return ( $html ) ? '<span class="growth negative">' . number_format($change, 2) . '%</span>' : number_format($change, 2) . '%';
	}
	else
	{
		return ( $html ) ? '<span class="growth even">&boxH;</span>' : '-';
	}

}
