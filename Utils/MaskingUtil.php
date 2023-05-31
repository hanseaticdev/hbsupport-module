<?php

namespace Modules\HbSupport\Utils;

use Str;

class MaskingUtil
{
    private const MIN_MASKED_CHAR_COUNT = 3;

    public function maskArray(array $parameters, $maskEverything = false): array
    {
        foreach ($parameters as $key => $parameter) {
            if (is_object($parameter)) {
                $parameters[$key] = $this->maskArray((array) $parameter, $maskEverything);

                continue;
            }
            if (is_array($parameter)) {
                $parameters[$key] = $this->maskArray($parameter, $maskEverything);

                continue;
            }
            if (Str::contains($key, 'url', true)) {
                $parameters[$key] = $this->maskUrl($parameter);

                continue;
            }
            if (Str::contains($key, 'email', true)) {
                $parameters[$key] = $this->maskEmail($parameter);

                continue;
            }
            if (Str::contains($key, ['phone', 'mobile', 'telefon', 'tel'], true)) {
                $parameters[$key] = $this->maskPhone($parameter);

                continue;
            }
            if (is_string($parameter) && ($maskEverything || $this->isSensitiveKey($key))) {
                $parameters[$key] = $this->maskString($parameter);
            }
        }

        return $parameters;
    }

    public function maskUrl(string $unmaskedUrl): string
    {
        $url = parse_url($unmaskedUrl);
        $return = '';
        if (isset($url['scheme'])) {
            $return .= $url['scheme'].':://';
        }
        if (isset($url['host'])) {
            $return .= $url['host'];
        }
        if (isset($url['path'])) {
            $return .= $url['path'];
        }

        return $return;
    }

    private function isSensitiveKey(string $key): bool
    {
        return Str::contains($key, config('hbsupport.masking.sensitive_keys'), true);
    }

    public function maskString(string $string): string
    {
        $len = mb_strlen($string);
        $clearCharBeginning = floor($len / 4);
        $maskedChars = $len - (2 * $clearCharBeginning);

        return mb_substr($string, 0, (int) $clearCharBeginning).
            str_pad('', (int) $maskedChars, '*').
            mb_substr($string, (int) ($clearCharBeginning + $maskedChars));
    }

    /**
     * masks given string behave on the given params
     *
     * @param  string  $clearStr
     * clear unmasked string
     * @param  int  $prefix
     * the count of the first chars of the data not get masked
     * @param  int  $suffix
     * the count of the last chars of the data not get masked
     * @return string returns the masked string
     */
    public function maskPhone(string $clearStr, int $prefix = 6, int $suffix = 3): string
    {
        //check given params
        if (! $clearStr) {
            return $clearStr;
        }
        $prefix = $prefix < 0 || ! $prefix ? 0 : $prefix;
        $suffix = $suffix < 0 || ! $suffix ? 0 : $suffix;

        return $this->doMasking($clearStr, $prefix, $suffix);
    }

    /**
     * masks given email
     *
     * hanseatic@test.de should be han******@test.de
     *
     * @return string returns the masked email
     */
    public function maskEmail(string $customerEmail): string
    {
        //given string check
        if (! $customerEmail) {
            return $customerEmail;
        }

        //split mail at @
        $splitMail = explode('@', $customerEmail);
        $prefix = 3;
        $suffix = 0;

        $maskedMailPre = $this->doMasking($splitMail[0], $prefix, $suffix);

        return $maskedMailPre.'@'.$splitMail[1];
    }

    /**
     * do masking
     *
     * @return string
     * returns the masked string
     */
    private function doMasking(string $unmaskedString, int $prefix, int $suffix): string
    {
        $strLen = mb_strlen($unmaskedString, 'UTF-8');

        //handle given string smaller than minMaskedCharCount
        if ($strLen <= self::MIN_MASKED_CHAR_COUNT) {
            $prefix = 0;
            $suffix = 0;
        } //handle given string smaller than (pre+suf)
        elseif (($strLen - self::MIN_MASKED_CHAR_COUNT) < ($prefix + $suffix)) {
            $prefix = 0;
            $suffix = ($strLen - self::MIN_MASKED_CHAR_COUNT);
        }

        //if suff is 0 method call is diff
        $maskedStrLen = $suffix ? mb_strlen(mb_substr($unmaskedString, $prefix, ($suffix * -1), 'UTF-8'), 'UTF-8') :
            mb_strlen(mb_substr($unmaskedString, $prefix, null, 'UTF-8'), 'UTF-8');
        $maskedStrPart = str_repeat('*', $maskedStrLen);

        $unmaskedReturnString = mb_substr($unmaskedString, 0, $prefix, 'UTF-8')
            .$maskedStrPart
            .mb_substr($unmaskedString, ($prefix + $maskedStrLen), $strLen, 'UTF-8');

        return str_replace('*', '∗', $unmaskedReturnString);
    }
}
