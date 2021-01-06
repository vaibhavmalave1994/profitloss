<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class GainsController extends Controller
{
    /**
     * Show the gains.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {   
        
        $result = []; //final result array 
        $transactionList = Transaction::distinct()->whereIn('type', ['buy', 'sell'])->get('ticker');
        $realisedGain = 0;
        $unRealisedGain = 0;
        foreach($transactionList as $keyt=>$transaction){
            $transactions = Transaction::where('ticker', $transaction->ticker)->whereIn('type', ['buy','sell'])->orderBy('date', 'asc')->get();
            $tempResult = [];
            $tempResult['position'] = "";
            $tempResult['sellQuantity'] = 0;
            $tempResult['buyQuantity'] = 0;
            $tempResult['sellAmount'] = 0;
            $tempResult['buyAmount'] = 0;
            foreach($transactions as $key=>$tran){
                if ($key == 0 && $tran->type =="sell"){
                    $tempResult['position'] = 'short';
                }elseif ($key == 0 && $tran->type =="buy"){
                    $tempResult['position'] = 'long';
                }
                if ( $tran->type =="sell"){
                    $tempResult['sellQuantity'] = $tempResult['sellQuantity']+($tran->quantity*-1);
                    $tempResult['sellAmount'] = $tempResult['sellAmount']+($tran->price*($tran->quantity*-1));
                }else{
                    $tempResult['buyQuantity'] = $tempResult['buyQuantity']+$tran->quantity;
                    $tempResult['buyAmount'] = $tempResult['buyAmount']+($tran->price*$tempResult['buyQuantity']);
                }
             
            }
            $result[$transaction->ticker] = $tempResult;
            if($tempResult['buyQuantity'] == 0){
                $result[$transaction->ticker]['realisedGain'] = 0;
                $result[$transaction->ticker]['unRealisedGain'] = $tempResult['sellAmount'];
            }elseif($tempResult['sellQuantity'] == 0){
                $result[$transaction->ticker]['realisedGain'] = 0;
                $result[$transaction->ticker]['unRealisedGain'] = $tempResult['buyAmount'];
            }else{
                $buyingAvrgPrice = $tempResult['buyAmount']/$tempResult['buyQuantity'];
                $sellingAvrgPrice = $tempResult['sellAmount']/$tempResult['sellQuantity'];
                if ($tempResult['position']== "short"){
                    $buyingPrice = $tempResult['sellQuantity'] * $buyingAvrgPrice;
                    $result[$transaction->ticker]['realisedGain'] = $tempResult['sellAmount']-$buyingPrice;
                    $result[$transaction->ticker]['unRealisedGain'] = ($tempResult['buyQuantity']-$tempResult['sellQuantity'])*$sellingAvrgPrice;
                }else{
                    $sellingPrice = $tempResult['buyQuantity'] * $sellingAvrgPrice;
                    $result[$transaction->ticker]['realisedGain'] = $tempResult['buyAmount']-$sellingPrice;
                    $result[$transaction->ticker]['unRealisedGain'] = ($tempResult['sellQuantity']-$tempResult['buyQuantity'])*$buyingAvrgPrice;
                }
            }
            $realisedGain += $result[$transaction->ticker]['realisedGain'];
            $unRealisedGain += $result[$transaction->ticker]['unRealisedGain']; 
        }
        // dd($result);
        // echo '<pre>';
        // print_r($result);
        // echo '</pre>';
        //  return view('welcome')->with('results', $result)->with('realisedGain', $realisedGain)->with('unRealisedGain', $unRealisedGain);
    }
}
