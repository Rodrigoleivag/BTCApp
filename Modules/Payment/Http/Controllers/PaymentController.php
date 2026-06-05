<?php

namespace Modules\Payment\Http\Controllers;

use App\Models\Deposits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Auth;
use App\Models\Auth\User;
use App\Models\Gateway;
use App\Models\Settings;
use App\Models\Financial\Currency;
use Session;
use Stripe\Stripe;
use Stripe\Token;
use Stripe\Charge;
use App\Lib\coinPayments;
use App\Lib\CoinPaymentHosted;
use App\Http\Controllers\Controller;

class PaymentController extends Controller
{

    public function depositConfirm(Request $request)
    {
        $user=User::find(Auth::user()->id);
        $gnl = Settings::first();
        $track = Session::get('Track');
        $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
        $currency=Currency::whereStatus(1)->first();
        if (is_null($data)) {
            return redirect()->route('user.fund')->with('alert', 'Invalid Deposit Request');
        }
        if ($data->status != 0) {
            return redirect()->route('user.fund')->with('alert', 'Invalid Deposit Request');
        }
        $gatewayData = Gateway::where('id', $data->gateway_id)->first();
        if ($data->gateway_id == 101) {
            $title = $gatewayData->name;
            $paypal['amount'] = $data->amount;
            $paypal['sendto'] = $gatewayData->val1;
            $paypal['track'] = $track;
            return view('user.payment.paypal', compact('paypal', 'gnl', 'currency', 'title'));
        } elseif ($data->gateway_id == 102) {
            $title = $gatewayData->name;
            $perfect['amount'] = $data->amount;
            $perfect['value1'] = $gatewayData->val1;
            $perfect['value2'] = $gatewayData->val2;
            $perfect['track'] = $track;
            $all = file_get_contents("https://blockchain.info/ticker");
            $res = json_decode($all);
            $btcrate = $res->USD->last;
            $usd = $data->amount;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);
            $data['btc_amo'] = $btc;
            $data->update();
            return view('user.payment.perfect', compact('perfect', 'gnl', 'currency', 'title'));
        } elseif ($data->gateway_id == 103) {
            $title = $gatewayData->name;
            $stripe['amount'] = $data->amount;
            $stripe['value1'] = $gatewayData->val1;
            $stripe['value2'] = $gatewayData->val2;
            $stripe['track'] = $track;
            $all = file_get_contents("https://blockchain.info/ticker");
            $res = json_decode($all);
            $btcrate = $res->USD->last;
            $usd = $data->amount;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);
            $data['btc_amo'] = $btc;
            $data->update();
            return view('user.payment.stripe', compact('stripe', 'title', 'gnl', 'currency'));
        } elseif ($data->gateway_id == 104) {
            $title = $gatewayData->name;
            $all = file_get_contents("https://blockchain.info/ticker");
            $res = json_decode($all);
            $btcrate = $res->USD->last;
            $usd = $data->amount;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);
            $data['btc_amo'] = $btc;
            $data->update();
            return view('user.payment.skrill', compact('title', 'gnl', 'currency', 'gatewayData', 'data'));
        } elseif ($data->gateway_id == 106) {
            $vogue['amount'] = $data->amount;
            $vogue['value1'] = $gatewayData->val1;
            $vogue['value2'] = $gatewayData->val2;
            $vogue['track'] = $track;
            $page_title = $gatewayData->name;
            $all = file_get_contents("https://blockchain.info/ticker");
            $res = json_decode($all);
            $btcrate = $res->USD->last;
            $usd = $data->amount;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);
            $data['btc_amo'] = $btc;
            $data->update();
            return view('user.payment.vogue', compact('vogue', 'page_title', 'gnl', 'currency', 'gatewayData', 'data'));
        } elseif ($data->gateway_id == 107) {
            $paystack['amount'] = $data->amount;
            $paystack['value1'] = $gatewayData->val1;
            $paystack['value2'] = $gatewayData->val2;
            $check['track'] = $track;
            $title = $gatewayData->name;
            $all = file_get_contents("https://blockchain.info/ticker");
            $res = json_decode($all);
            $btcrate = $res->USD->last;
            $usd = $data->amount;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);
            $data['btc_amo'] = $btc;
            $data->update();
            return view('user.payment.paystack', compact('paystack', 'track', 'title', 'gnl', 'currency', 'gatewayData', 'data'));
        } elseif ($data->gateway_id == 108) {
            $flutter['amount'] = $data->amount;
            $flutter['value1'] = $gatewayData->val1;
            $flutter['value2'] = $gatewayData->val2;
            $flutter['track'] = $track;
            $title = $gatewayData->name;
            $all = file_get_contents("https://blockchain.info/ticker");
            $res = json_decode($all);
            $btcrate = $res->USD->last;
            $usd = $data->amount;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);
            $data['btc_amo'] = $btc;
            $data->update();
            return view('user.payment.flutter', compact('flutter', 'title', 'gnl', 'currency', 'gatewayData', 'data'));
        } elseif ($data->gateway_id == 501) {
            $title = $gatewayData->name;
            $all = file_get_contents("https://blockchain.info/ticker");
            $res = json_decode($all);
            $btcrate = $res->USD->last;
            $usd = $data->amount;
            $btcamount = $usd / $btcrate;
            $btc = round($btcamount, 8);
            $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $blockchain_root = "https://blockchain.info/";
                $blockchain_receive_root = "https://api.blockchain.info/";
                $mysite_root = url('/');
                $secret = "ABIR";
                $my_xpub = $gatewayData->val2;
                $my_api_key = $gatewayData->val1;
                $invoice_id = $track;
                $callback_url = $mysite_root . "/ipnbtc?invoice_id=" . $invoice_id . "&secret=" . $secret;
                $resp = @file_get_contents($blockchain_receive_root . "v2/receive?key=" . $my_api_key . "&callback=" . urlencode($callback_url) . "&xpub=" . $my_xpub);
                if (!$resp) {
                    return redirect()->route('user.fund')->with('alert', 'BLOCKCHAIN API HAVING ISSUE. PLEASE TRY LATER');
                }
                $response = json_decode($resp);
                $sendto = $response->address;
                $data['btc_wallet'] = $sendto;
                $data['btc_amo'] = $btc;
                $data->update();
            }
            $DepositData = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
            $bitcoin['amount'] = $DepositData->btc_amo;
            $bitcoin['sendto'] = $DepositData->btc_wallet;
            $var = "bitcoin:$DepositData->btc_wallet?amount=$DepositData->btc_amo";
            $bitcoin['code'] = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=$var&choe=UTF-8\" title='' style='width:300px;' />";
            return view('user.payment.blockchain', compact('bitcoin', 'title'));
        } elseif ($data->gateway_id == 505) {
            $method = Gateway::find(505);
            if ($data->btc_amo == 0 || $data->btc_wallet == "") {
                $cps = new CoinPaymentHosted();
                $cps->Setup($method->val2, $method->val1);
                $callbackUrl = route('ipn.coinPay.btc');
                $req = array(
                    'amount' => $data->amount,
                    'currency1' => 'USD',
                    'currency2' => 'BTC',
                    'custom' => $data->trx,
                    'ipn_url' => $callbackUrl,
                );
                $result = $cps->CreateTransaction($req);
                if ($result['error'] == 'ok') {
                    $data['status_url'] = $result['result']['status_url'];
                    $data['txn_id'] = $result['result']['txn_id'];
                    $bcoin = sprintf('%.08f', $result['result']['amount']);
                    $sendadd = $result['result']['address'];
                    $data['btc_amo'] = $bcoin;
                    $data['btc_wallet'] = $sendadd;
                    $data->update();
                } else {
                    return back()->with('alert', 'Failed to Process');
                }
            }
            $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
            $wallet = $data['btc_wallet'];
            $bcoin = $data['btc_amo'];
            $title = "Deposit via  ".$method->name;
            $qrurl = "<img src=\"https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=bitcoin:$wallet&choe=UTF-8\" title='' style='width:300px;' />";
            return view('user.payment.coinpaybtc', compact('bcoin', 'wallet', 'qrurl', 'title'));
        }
    }


    //IPN Functions //////
    public function ipnpaypal()
    {
        $raw_post_data = file_get_contents('php://input');
        $raw_post_array = explode('&', $raw_post_data);
        $myPost = array();
        foreach ($raw_post_array as $keyval) {
            $keyval = explode('=', $keyval);
            if (count($keyval) == 2)
                $myPost[$keyval[0]] = urldecode($keyval[1]);
        }

        $req = 'cmd=_notify-validate';
        if (function_exists('get_magic_quotes_gpc')) {
            $get_magic_quotes_exists = true;
        }
        foreach ($myPost as $key => $value) {
            if ($get_magic_quotes_exists == true && get_magic_quotes_gpc() == 1) {
                $value = urlencode(stripslashes($value));
            } else {
                $value = urlencode($value);
            }
            $req .= "&$key=$value";
        }

        $paypalURL = "https://ipnpb.paypal.com/cgi-bin/webscr?";
        $callUrl = $paypalURL . $req;
        $verify = file_get_contents($callUrl);
        if ($verify == "VERIFIED") {
            //PAYPAL VERIFIED THE PAYMENT
            $receiver_email = isset($_POST['receiver_email']) ? filter_var($_POST['receiver_email'], FILTER_SANITIZE_EMAIL) : '';
            $mc_currency = isset($_POST['mc_currency']) ? filter_var($_POST['mc_currency'], FILTER_SANITIZE_STRING) : '';
            $mc_gross = isset($_POST['mc_gross']) ? filter_var($_POST['mc_gross'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
            $track = isset($_POST['custom']) ? filter_var($_POST['custom'], FILTER_SANITIZE_STRING) : '';

            //GRAB DATA FROM DATABASE!!
            $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
            $gatewayData = Gateway::find(101);
            $amount = $data->amount;

            if ($receiver_email == $gatewayData->val1 && $mc_currency == "USD" && $mc_gross == $amount && $data->status == '0') {
                //Update User Data
                return redirect()->route('deposit.verify', ['id' => $data->secret]);
            }
        }

    }

    public function ipnperfect()
    {
        $gatewayData = Gateway::find(102);
        $passphrase = strtoupper(md5($gatewayData->val2));

        define('ALTERNATE_PHRASE_HASH', $passphrase);
        define('PATH_TO_LOG', '/somewhere/out/of/document_root/');
        
        // Sanitize POST inputs before use
        $payment_id = isset($_POST['PAYMENT_ID']) ? filter_var($_POST['PAYMENT_ID'], FILTER_SANITIZE_STRING) : '';
        $payee_account = isset($_POST['PAYEE_ACCOUNT']) ? filter_var($_POST['PAYEE_ACCOUNT'], FILTER_SANITIZE_STRING) : '';
        $payment_amount = isset($_POST['PAYMENT_AMOUNT']) ? filter_var($_POST['PAYMENT_AMOUNT'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
        $payment_units = isset($_POST['PAYMENT_UNITS']) ? filter_var($_POST['PAYMENT_UNITS'], FILTER_SANITIZE_STRING) : '';
        $payment_batch_num = isset($_POST['PAYMENT_BATCH_NUM']) ? filter_var($_POST['PAYMENT_BATCH_NUM'], FILTER_SANITIZE_STRING) : '';
        $payer_account = isset($_POST['PAYER_ACCOUNT']) ? filter_var($_POST['PAYER_ACCOUNT'], FILTER_SANITIZE_STRING) : '';
        $timestampgmt = isset($_POST['TIMESTAMPGMT']) ? filter_var($_POST['TIMESTAMPGMT'], FILTER_SANITIZE_NUMBER_INT) : '';
        
        $string = $payment_id . ':' . $payee_account . ':' .
            $payment_amount . ':' . $payment_units . ':' .
            $payment_batch_num . ':' .
            $payer_account . ':' . ALTERNATE_PHRASE_HASH . ':' .
            $timestampgmt;

        $hash = strtoupper(md5($string));
        $hash2 = isset($_POST['V2_HASH']) ? $_POST['V2_HASH'] : '';

        if ($hash == $hash2) {

            $amo = $payment_amount;
            $unit = $payment_units;
            $track = $payment_id;

            $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
            $gnl = Settings::first();

            if ($payee_account == $gatewayData->val1 && $unit == "USD" && $amo == $data->amount && $data->status == '0') {
                //Update User Data
                return redirect()->route('deposit.verify', ['id' => $data->secret]);
            }
        }

    }

    public function ipnstripe(Request $request)
    {
        $currency=Currency::whereStatus(1)->first();
        $track = Session::get('Track');
        $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();

        $this->validate($request,
            [
                'cardNumber' => 'required',
                'cardExpiry' => 'required',
                'cardCVC' => 'required',
            ]);

        $cc = $request->cardNumber;
        $exp = $request->cardExpiry;
        $cvc = $request->cardCVC;

        // Use validated request data instead of raw $_POST
        $exp = $pieces = explode("/", $request->cardExpiry);
        $emo = trim($exp[0]);
        $eyr = trim($exp[1]);
        $cnts = round($data->amount, 2) * 100;

        $gatewayData = Gateway::find(103);
        $gnl = Settings::first();

        Stripe::setApiKey($gatewayData->val1);

        try {
            $token = Token::create(array(
                "card" => array(
                    "number" => "$cc",
                    "exp_month" => $emo,
                    "exp_year" => $eyr,
                    "cvc" => "$cvc"
                )
            ));

            try {
                $charge = Charge::create(array(
                    'card' => $token['id'],
                    'currency' => $currency->name,
                    'amount' => $cnts,
                    'description' => 'item',
                ));

                if ($charge['status'] == 'succeeded') {
                    //Update User Data
                    return redirect()->route('deposit.verify', ['id' => $data->secret]);
                    return redirect()->route('user.fund')->with('success', 'Success');
                }
            } catch (Exception $e) {
                return redirect()->route('user.fund')->with('alert', $e->getMessage());
            }

        } catch (Exception $e) {
            return redirect()->route('user.fund')->with('alert', $e->getMessage());
        }

    }

    public function skrillIPN()
    {
         $track = Session::get('Track');
        $skrill = Gateway::find(104);
        
        // Sanitize POST inputs
        $merchant_id = isset($_POST['merchant_id']) ? filter_var($_POST['merchant_id'], FILTER_SANITIZE_STRING) : '';
        $transaction_id = isset($_POST['transaction_id']) ? filter_var($_POST['transaction_id'], FILTER_SANITIZE_STRING) : '';
        $mb_amount = isset($_POST['mb_amount']) ? filter_var($_POST['mb_amount'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : '';
        $mb_currency = isset($_POST['mb_currency']) ? filter_var($_POST['mb_currency'], FILTER_SANITIZE_STRING) : '';
        $status = isset($_POST['status']) ? filter_var($_POST['status'], FILTER_SANITIZE_NUMBER_INT) : '';
        $md5sig = isset($_POST['md5sig']) ? $_POST['md5sig'] : '';
        $pay_to_email = isset($_POST['pay_to_email']) ? filter_var($_POST['pay_to_email'], FILTER_SANITIZE_EMAIL) : '';
        
        $concatFields = $merchant_id
            . $transaction_id
            . strtoupper(md5($skrill->val2))
            . $mb_amount
            . $mb_currency
            . $status;

        $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
        $gnl = Settings::first();

        if (strtoupper(md5($concatFields)) == $md5sig && $status == 2 && $pay_to_email == $skrill->val1 && $data->status == '0') {
            //Update User Data
            return redirect()->route('deposit.verify', ['id' => $data->secret]);

        }
    }
        
    public function flutterIPN()
    {
		$track = Session::get('Track');
        $flutter = Gateway::find(108);
        $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
        return redirect()->route('deposit.verify', ['id' => $data->secret]);
    }     

    public function paystackIPN()
    {
        $track = Session::get('Track');
        $paystack = Gateway::find(107);
        $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
        $result = array();
        //The parameter after verify/ is the transaction reference to be verified
        $url = 'https://api.paystack.co/transaction/verify/'.$track;
    
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt(
          $ch, CURLOPT_HTTPHEADER, [
            'Authorization: Bearer '.$paystack->val2]
        );
        $request = curl_exec($ch);
        if(curl_error($ch)){
        echo 'error:' . curl_error($ch);
        }
        curl_close($ch);
    
        if ($request) {
          $result = json_decode($request, true);
        }
        if (array_key_exists('data', $result) && array_key_exists('status', $result['data']) && ($result['data']['status'] === 'success')) {
            return redirect()->route('deposit.verify', ['id' => $data->secret]);
        //Perform necessary action
        }
    }

    public function ipnBchain()
    {
        $gatewayData = Gateway::find(501);

        $track = $_GET['invoice_id'];
        $secret = $_GET['secret'];
        $address = $_GET['address'];
        $value = $_GET['value'];
        $confirmations = $_GET['confirmations'];
        $value_in_btc = $_GET['value'] / 100000000;

        $trx_hash = $_GET['transaction_hash'];

        $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();


        if ($data->status == 0) {
            if ($data->btc_amo == $value_in_btc && $data->btc_wallet == $address && $secret == "ABIR" && $confirmations > 2) {

                //Update User Data
                return redirect()->route('deposit.verify', ['id' => $data->secret]);
            }
        }

    }

    public function ipnCoinPayBtc(Request $request)
    {
        $track = $request->custom;
        $status = $request->status;
        $amount2 = floatval($request->amount2);
        $currency2 = $request->currency2;

        $data = Deposits::where('trx', $track)->orderBy('id', 'DESC')->first();
        $bcoin = $data->btc_amo;
        if ($status >= 100 || $status == 2) {
            if ($currency2 == "BTC" && $data->status == '0' && $data->btc_amo <= $amount2) {
                return redirect()->route('deposit.verify', ['id' => $data->secret]);
            }
        }
    } 

}
