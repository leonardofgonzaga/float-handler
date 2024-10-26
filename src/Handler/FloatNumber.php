<?php

namespace FloatHandler\FloatHandler\Handler;

class FloatNumber
{
    /**
     * Float number value
     */
    private float $value;

    /**
     * The number of decimal places
     */
    private float $decimals;

    /**
     * The acceptable difference to consider two floats equal
     */
    private float $epsilon;

    /**
     * Constructor to set the value and the  precision (epsilon) for comparisons.
     *
     * @param float $value The float number value
     * @param int $decimals The number of decimal places
     * @param float $epsilon The acceptable difference to consider two floats equal.
     */
    public function __construct(float $value = 0, int $decimals = 2, float $epsilon = 0.00001)
    {
        $this->decimals = $decimals;
        $this->value = $this->exact($value);
        $this->epsilon = $epsilon;
    }

    /**
     * Returns the current value of this object
     *
     * @param string $currencyCode The ISO currency code (e.g., "USD", "EUR"). (optional)
     * @param string $locale The locale code (e.g., "en_US", "de_DE"). (optional)
     * @param bool $removePrefix Removes the currency prefix. (optional)
     * @return float|string Returns the float value of this object or a beautifully formatted currency string.
     */
    public function getValue(string $currencyCode = '', string $locale = 'en_US', bool $removePrefix = false): float|string
    {
        if (empty($currencyCode)) {
            return $this->exact($this->value);
        }

        if (empty($locale)) {
           throw new \Exception("Locale is required when there is acurrency code");           
        }

        $formatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
        $formattedCurrency = $formatter->formatCurrency($this->exact($this->value), $currencyCode);

        if ($removePrefix) {
            $currencySymbol = $formatter->getSymbol(\NumberFormatter::CURRENCY_SYMBOL);
            $formattedCurrency = str_replace($currencySymbol, '', $formattedCurrency);
        }

        return trim($formattedCurrency);
    }

    /**
     * Set the value of decimals places
     */
    public function setDecimals(int $decimals)
    {
        $this->decimals = $decimals;
    }

    /**
     * Increases the number entered in the current value
     * 
     * @param FloatNumber $number Number to be increased 
     */
    public function increase(FloatNumber $number): void
    {
        $this->value += $this->exact($number->getValue());
    }

    /**
     * Decreases the number entered in the current value
     * 
     * @param FloatNumber $number Number to be decreased 
     */
    public function decrease(FloatNumber $number): void
    {
        $this->value -= $this->exact($number->getValue());
    }

    /**
     * Multiplies the current value by the value entered
     * 
     * @param FloatNumber $number Number to be multiplied 
     * @return FloatNumber Returns the result as a FloatNumber object setted 
     * with current decimals and epsilon values
     */
    public function times(FloatNumber $number): FloatNumber
    {
        $result = $this->value * $this->exact($number->getValue());

        return new FloatNumber(
            $result,
            $this->decimals,
            $this->epsilon
        );
    }

    /**
     * Divides the current value by entered value 
     * 
     * @param FloatNumber $number Number to divide by 
     * @return FloatNumber Returns the result as a FloatNumber object setted 
     * with current decimals and epsilon values
     */
    public function dividedBy(FloatNumber $number): FloatNumber
    {
        $result = $this->value / $this->exact($number->getValue());

        return new FloatNumber(
            $result,
            $this->decimals,
            $this->epsilon
        );
    }

    /**
     * Compares current value with entered value.
     *
     * @param FloatNumber $number The number to be compared with.
     * @return bool True if the numbers are equal, false otherwise.
     */
    public function isEqual(FloatNumber $number): bool
    {
        return abs($this->value - $number->getValue()) < $this->epsilon;
    }

    /**
     * Checks if current value is greater than entered value.
     *
     * @param FloatNumber $number Number to be compared with.
     * @return bool True if current value greater than entered value, false otherwise.
     */
    public function isGreaterThan(FloatNumber $number): bool
    {
        return ($this->value - $number->getValue()) > $this->epsilon;
    }

    /**
     * Checks if current value is less than entered value.
     *
     * @param FloatNumber $number Number to be compared with.
     * @return bool True if current value less than entered value, false otherwise.
     */
    public function isLessThan(FloatNumber $number): bool
    {
        return ($number->getValue() - $this->value) > $this->epsilon;
    }

    /**
     *  Converts a float number to an exect value.
     * 
     * @param float $number The number to be converted
     * @param bool $round Round the entered value
     */
    public function exact(float $number, bool $round = true): float
    {
        if ($round) {
            $number = round($number,$this->decimals);
        }

        $formattedNumber = number_format($number, $this->decimals, '.', '');
        return (float) $formattedNumber;
    }
}
