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

        //fetch distinct ticker with transaction type buy / sell
        $tickerList = Transaction::distinct()->whereIn('type', ['buy', 'sell'])->get('ticker');
        
        //initialised variables for total gain calculation
        $realisedGain = 0;
        $unRealisedGain = 0;

        //for all ticker do gain calculation individually
        foreach($tickerList as $keyt=>$ticker){
            //get all buy and sell transactions for ticker in ascending order of date 
            $transactions = Transaction::where('ticker', $ticker->ticker)->whereIn('type', ['buy','sell'])->orderBy('date', 'asc')->get();
            $tempResult = []; //array to store ticker info
            //initialise default values for data
            $tempResult['position'] = ""; 
            $tempResult['sellQuantity'] = 0;
            $tempResult['buyQuantity'] = 0;
            $tempResult['sellAmount'] = 0;
            $tempResult['buyAmount'] = 0;

            //for each transaction fetch do calculations
            foreach($transactions as $key=>$tran){
                //first find position/trade type will need this to do gain calculation
                //if first transaction type is sell then position is short position
                if ($key == 0 && $tran->type =="sell"){
                    $tempResult['position'] = 'short';
                }elseif ($key == 0 && $tran->type =="buy"){
                    //if first transaction type is buy then position is long position
                    $tempResult['position'] = 'long';
                }

                //as per transcation type keep adding quantity and amount to get total amount spent and total quantity traded for each type
                if ( $tran->type =="sell"){
                    $tempResult['sellQuantity'] = $tempResult['sellQuantity']+($tran->quantity*-1);
                    $tempResult['sellAmount'] = $tempResult['sellAmount']+($tran->price*($tran->quantity*-1));
                }else{
                    
                    $tempResult['buyQuantity'] = $tempResult['buyQuantity']+$tran->quantity;
                    $tempResult['buyAmount'] = $tempResult['buyAmount']+($tran->price*$tran->quantity);
                }
             
            }

            //set temp data against ticker
            $result[$ticker->ticker] = $tempResult;

            //if buyQuantity is 0 then ticker is not sold yet..so this will be unrealised gain 
            if($tempResult['buyQuantity'] == 0){
                $result[$ticker->ticker]['realisedGain'] = 0;
                $result[$ticker->ticker]['unRealisedGain'] = $tempResult['sellAmount'];
            }elseif($tempResult['sellQuantity'] == 0){
                //if sellQuantity is 0 then ticker is not bought yet..so this will be unrealised gain 
                $result[$ticker->ticker]['realisedGain'] = 0;
                $result[$ticker->ticker]['unRealisedGain'] = $tempResult['buyAmount'];
            }else{
                //if ticker has both type of transactions sell and buy then calcluate gains
                //calculate avg buying and selling price.
                $buyingAvrgPrice = $tempResult['buyAmount']/$tempResult['buyQuantity'];
                $sellingAvrgPrice = $tempResult['sellAmount']/$tempResult['sellQuantity'];
                
                if ($tempResult['position']== "short"){
                    //if position is short
                    //realisedgain = (buying quantity* selling price)- (buying quantiy * buying price)
                    //unrealisedgain = ((selling quantity - buying qunatity)* buying price)- (selling quantiy * selling price)
                    $sellingPrice = $tempResult['buyQuantity'] * $sellingAvrgPrice;
                    $result[$ticker->ticker]['realisedGain'] = $sellingPrice-$tempResult['buyAmount'];
                    $result[$ticker->ticker]['unRealisedGain'] = (($tempResult['sellQuantity'] - $tempResult['buyQuantity'])*$sellingAvrgPrice)-(($tempResult['sellQuantity'] - $tempResult['buyQuantity'])*$buyingAvrgPrice);
                }else{
                     //if position is short
                    //realisedgain = (selling quantity* selling price)- (selling quantiy * buying price)
                    //unrealisedgain = ((selling quantity - buying qunatity)* buying price)- (selling quantiy * selling price)
                    $buyingPrice = $tempResult['sellQuantity'] * $buyingAvrgPrice;
                    $result[$ticker->ticker]['realisedGain'] = $tempResult['sellAmount']-$buyingPrice;
                    $result[$ticker->ticker]['unRealisedGain'] = (($tempResult['buyQuantity'] - $tempResult['sellQuantity'])*$sellingAvrgPrice)-(($tempResult['buyQuantity'] - $tempResult['sellQuantity'])*$buyingAvrgPrice);
                }
            }
            //total gains 
            $realisedGain += $result[$ticker->ticker]['realisedGain'];
            $unRealisedGain += $result[$ticker->ticker]['unRealisedGain']; 
        }
        // incase array needed to print
        // echo '<pre>';
        // print_r($result);
        // echo '</pre>';
         return view('welcome')->with('results', $result)->with('realisedGain', $realisedGain)->with('unRealisedGain', $unRealisedGain);
    }
}
