<?php
/**
 * PaytrailGateway class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.components
 */

use NordSoftware\Paytrail\Object\Payment;
use NordSoftware\Paytrail\Object\Product;

class PaytrailGateway extends PaymentGateway
{
    /**
     * @var string
     */
    public $apiKey = '13466';

    /**
     * @var string
     */
    public $apiSecret = '6pKF4jkv97zmqBJ3ZL8gUw5DfT2NMQ';

    /**
     * @var int
     */
    public $apiVersion = 1;

    /**
     * @var string
     */
    public $successRoute = 'paytrail/success';

    /**
     * @var string
     */
    public $failureRoute = 'paytrail/failure';

    /**
     * @var string
     */
    public $notificationRoute = 'paytrail/notify';

    /**
     * @var string
     */
    public $pendingRoute;

    /**
     * @var string
     */
    public $defaultLocale = Payment::LOCALE_ENUS;

    /**
     * @var array
     */
    protected static $supportedLocales = array(
        Payment::LOCALE_ENUS,
        Payment::LOCALE_FIFI,
        Payment::LOCALE_SVSE,
    );

    /**
     * @var \NordSoftware\Paytrail\Http\Client
     */
    protected $_client;

    /**
     * Initializes this gateway.
     */
    public function init()
    {
        parent::init();
        $this->_client = $this->createClient();
    }

    /**
     * @param PaymentTransaction $transaction
     * @throws Exception
     */
    public function handleTransaction($transaction)
    {
        $attributes = array();
        foreach (array('success', 'failure', 'notification', 'pending') as $attribute) {
            if (isset($this->{$attribute . 'Route'})) {
                $attributes[$attribute . 'Url'] = Yii::app()->createAbsoluteUrl($this->{$attribute . 'Route'});
            }
        }
        $urlset = PaytrailUrlset::create($attributes);

        $address = PaytrailAddress::create(
            array(
                'streetAddress' => $transaction->shippingContact->streetAddress,
                'postalCode' => $transaction->shippingContact->postalCode,
                'postOffice' => $transaction->shippingContact->postOffice,
                'countryCode' => $this->normalizeCountry($transaction->shippingContact->countryCode),
            )
        );

        $contact = PaytrailContact::create(
            array(
                'firstName' => $transaction->shippingContact->firstName,
                'lastName' => $transaction->shippingContact->lastName,
                'email' => $transaction->shippingContact->email,
                'phoneNumber' => $transaction->shippingContact->phoneNumber,
                'mobileNumber' => $transaction->shippingContact->mobileNumber,
                'companyName' => $transaction->shippingContact->companyName,
                'addressId' => $address->id,
            )
        );

        $payment = PaytrailPayment::create(
            array(
                'orderNumber' => $transaction->orderIdentifier,
                'referenceNumber' => $transaction->referenceNumber,
                'description' => $transaction->description,
                'contactId' => $contact->id,
                'urlsetId' => $urlset->id,
                'locale' => $this->normalizeLocale($transaction->locale),
            )
        );

        foreach ($transaction->items as $item) {
            PaytrailProduct::create(
                array(
                    'paymentId' => $payment->id,
                    'title' => $item->description,
                    'code' => $item->code,
                    'quantity' => (float)$item->quantity,
                    'price' => $item->price,
                    'vat' => $item->vat,
                    'discount' => $item->discount,
                    'type' => Product::TYPE_NORMAL,
                )
            );
        }

        try {
            $this->onBeforeProcessTransaction($this->createEvent($transaction));
            $response = $this->_client->processPayment($payment->toObject());
            $result = PaytrailResult::create(
                array(
                    'paymentId' => $payment->id,
                    'orderNumber' => $response->getOrderNumber(),
                    'token' => $response->getToken(),
                    'url' => $response->getUrl(),
                )
            );
            $this->onAfterProcessTransaction($this->createEvent($transaction));
            Yii::app()->controller->redirect($result->url);
        } catch (Exception $e) {
            $this->onTransactionFailed($this->createEvent($transaction));
            throw $e;
        }
    }

    /**
     * @return \NordSoftware\Paytrail\Http\Client
     */
    protected function createClient()
    {
        $client = new NordSoftware\Paytrail\Http\Client;
        $client->configure(
            array(
                'apiKey' => $this->apiKey,
                'apiSecret' => $this->apiSecret,
                'apiVersion' => $this->apiVersion,
            )
        );
        $client->connect();
        return $client;
    }

    /**
     * @param $country
     * @return mixed
     * @throws CException
     */
    protected function normalizeCountry($country)
    {
        static $alpha2ToAlpha3 = array(
            'AF' => 'AFG',
            'AX' => 'ALA',
            'AL' => 'ALB',
            'DZ' => 'DZA',
            'AS' => 'ASM',
            'AD' => 'AND',
            'AO' => 'AGO',
            'AI' => 'AIA',
            'AQ' => 'ATA',
            'AG' => 'ATG',
            'AR' => 'ARG',
            'AM' => 'ARM',
            'AW' => 'ABW',
            'AU' => 'AUS',
            'AT' => 'AUT',
            'AZ' => 'AZE',
            'BS' => 'BHS',
            'BH' => 'BHR',
            'BD' => 'BGD',
            'BB' => 'BRB',
            'BY' => 'BLR',
            'BE' => 'BEL',
            'BZ' => 'BLZ',
            'BJ' => 'BEN',
            'BM' => 'BMU',
            'BT' => 'BTN',
            'BO' => 'BOL',
            'BQ' => 'BES',
            'BA' => 'BIH',
            'BW' => 'BWA',
            'BV' => 'BVT',
            'BR' => 'BRA',
            'IO' => 'IOT',
            'BN' => 'BRN',
            'BG' => 'BGR',
            'BF' => 'BFA',
            'BI' => 'BDI',
            'KH' => 'KHM',
            'CM' => 'CMR',
            'CA' => 'CAN',
            'CV' => 'CPV',
            'KY' => 'CYM',
            'CF' => 'CAF',
            'TD' => 'TCD',
            'CL' => 'CHL',
            'CN' => 'CHN',
            'CX' => 'CXR',
            'CC' => 'CCK',
            'CO' => 'COL',
            'KM' => 'COM',
            'CG' => 'COG',
            'CD' => 'COD',
            'CK' => 'COK',
            'CR' => 'CRI',
            'CI' => 'CIV',
            'HR' => 'HRV',
            'CU' => 'CUB',
            'CW' => 'CUW',
            'CY' => 'CYP',
            'CZ' => 'CZE',
            'DK' => 'DNK',
            'DJ' => 'DJI',
            'DM' => 'DMA',
            'DO' => 'DOM',
            'EC' => 'ECU',
            'EG' => 'EGY',
            'SV' => 'SLV',
            'GQ' => 'GNQ',
            'ER' => 'ERI',
            'EE' => 'EST',
            'ET' => 'ETH',
            'FK' => 'FLK',
            'FO' => 'FRO',
            'FJ' => 'FJI',
            'FI' => 'FIN',
            'FR' => 'FRA',
            'GF' => 'GUF',
            'PF' => 'PYF',
            'TF' => 'ATF',
            'GA' => 'GAB',
            'GM' => 'GMB',
            'GE' => 'GEO',
            'DE' => 'DEU',
            'GH' => 'GHA',
            'GI' => 'GIB',
            'GR' => 'GRC',
            'GL' => 'GRL',
            'GD' => 'GRD',
            'GP' => 'GLP',
            'GU' => 'GUM',
            'GT' => 'GTM',
            'GG' => 'GGY',
            'GN' => 'GIN',
            'GW' => 'GNB',
            'GY' => 'GUY',
            'HT' => 'HTI',
            'HM' => 'HMD',
            'VA' => 'VAT',
            'HN' => 'HND',
            'HK' => 'HKG',
            'HU' => 'HUN',
            'IS' => 'ISL',
            'IN' => 'IND',
            'ID' => 'IDN',
            'IR' => 'IRN',
            'IQ' => 'IRQ',
            'IE' => 'IRL',
            'IM' => 'IMN',
            'IL' => 'ISR',
            'IT' => 'ITA',
            'JM' => 'JAM',
            'JP' => 'JPN',
            'JE' => 'JEY',
            'JO' => 'JOR',
            'KZ' => 'KAZ',
            'KE' => 'KEN',
            'KI' => 'KIR',
            'KP' => 'PRK',
            'KR' => 'KOR',
            'KW' => 'KWT',
            'KG' => 'KGZ',
            'LA' => 'LAO',
            'LV' => 'LVA',
            'LB' => 'LBN',
            'LS' => 'LSO',
            'LR' => 'LBR',
            'LY' => 'LBY',
            'LI' => 'LIE',
            'LT' => 'LTU',
            'LU' => 'LUX',
            'MO' => 'MAC',
            'MK' => 'MKD',
            'MG' => 'MDG',
            'MW' => 'MWI',
            'MY' => 'MYS',
            'MV' => 'MDV',
            'ML' => 'MLI',
            'MT' => 'MLT',
            'MH' => 'MHL',
            'MQ' => 'MTQ',
            'MR' => 'MRT',
            'MU' => 'MUS',
            'YT' => 'MYT',
            'MX' => 'MEX',
            'FM' => 'FSM',
            'MD' => 'MDA',
            'MC' => 'MCO',
            'MN' => 'MNG',
            'ME' => 'MNE',
            'MS' => 'MSR',
            'MA' => 'MAR',
            'MZ' => 'MOZ',
            'MM' => 'MMR',
            'NA' => 'NAM',
            'NR' => 'NRU',
            'NP' => 'NPL',
            'NL' => 'NLD',
            'NC' => 'NCL',
            'NZ' => 'NZL',
            'NI' => 'NIC',
            'NE' => 'NER',
            'NG' => 'NGA',
            'NU' => 'NIU',
            'NF' => 'NFK',
            'MP' => 'MNP',
            'NO' => 'NOR',
            'OM' => 'OMN',
            'PK' => 'PAK',
            'PW' => 'PLW',
            'PS' => 'PSE',
            'PA' => 'PAN',
            'PG' => 'PNG',
            'PY' => 'PRY',
            'PE' => 'PER',
            'PH' => 'PHL',
            'PN' => 'PCN',
            'PL' => 'POL',
            'PT' => 'PRT',
            'PR' => 'PRI',
            'QA' => 'QAT',
            'RE' => 'REU',
            'RO' => 'ROU',
            'RU' => 'RUS',
            'RW' => 'RWA',
            'BL' => 'BLM',
            'SH' => 'SHN',
            'KN' => 'KNA',
            'LC' => 'LCA',
            'MF' => 'MAF',
            'PM' => 'SPM',
            'VC' => 'VCT',
            'WS' => 'WSM',
            'SM' => 'SMR',
            'ST' => 'STP',
            'SA' => 'SAU',
            'SN' => 'SEN',
            'RS' => 'SRB',
            'SC' => 'SYC',
            'SL' => 'SLE',
            'SG' => 'SGP',
            'SX' => 'SXM',
            'SK' => 'SVK',
            'SI' => 'SVN',
            'SB' => 'SLB',
            'SO' => 'SOM',
            'ZA' => 'ZAF',
            'GS' => 'SGS',
            'SS' => 'SSD',
            'ES' => 'ESP',
            'LK' => 'LKA',
            'SD' => 'SDN',
            'SR' => 'SUR',
            'SJ' => 'SJM',
            'SZ' => 'SWZ',
            'SE' => 'SWE',
            'CH' => 'CHE',
            'SY' => 'SYR',
            'TW' => 'TWN',
            'TJ' => 'TJK',
            'TZ' => 'TZA',
            'TH' => 'THA',
            'TL' => 'TLS',
            'TG' => 'TGO',
            'TK' => 'TKL',
            'TO' => 'TON',
            'TT' => 'TTO',
            'TN' => 'TUN',
            'TR' => 'TUR',
            'TM' => 'TKM',
            'TC' => 'TCA',
            'TV' => 'TUV',
            'UG' => 'UGA',
            'UA' => 'UKR',
            'AE' => 'ARE',
            'GB' => 'GBR',
            'US' => 'USA',
            'UM' => 'UMI',
            'UY' => 'URY',
            'UZ' => 'UZB',
            'VU' => 'VUT',
            'VE' => 'VEN',
            'VN' => 'VNM',
            'VG' => 'VGB',
            'VI' => 'VIR',
            'WF' => 'WLF',
            'EH' => 'ESH',
            'YE' => 'YEM',
            'ZM' => 'ZMB',
            'ZW' => 'ZWE',
        );

        // alpha2 country code
        if (strlen($country) === 2 && isset($alpha2ToAlpha3[$country])) {
            return $country;
        }

        $alpha3ToAlpha2 = array_flip($alpha2ToAlpha3);

        // alpha3 country code
        if (strlen($country) === 3 && isset($alpha3ToAlpha2[$country])) {
            return $alpha3ToAlpha2[$country];
        }

        // invalid country code
        throw new CException(sprintf('Failed to normalize country "%s".', $country));
    }

    /**
     * @param $locale
     * @return string
     */
    protected function normalizeLocale($locale)
    {
        if (strpos($locale, '_')) {
            $parts = explode('_', $locale);
            $locale = $parts[0] . '_' . strtoupper($parts[1]);
        }
        return in_array($locale, self::$supportedLocales) ? $locale : $this->defaultLocale;
    }
}