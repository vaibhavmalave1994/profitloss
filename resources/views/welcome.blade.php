<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Profit Loss statment</title>

        <style>
            body {
                font-family: 'Nunito';
            }
            table, th, td {
            border: 1px solid black;
            border-collapse: collapse;
            }
            .short, .loss{
                background-color:red;
            }
            .long, .profit{
                background-color:green;
            }
        </style>
    </head>
    <body>
        <div class="relative flex items-top justify-center min-h-screen bg-gray-100 dark:bg-gray-900 sm:items-center sm:pt-0">
            

                <div class="mt-8 bg-white  overflow-hidden shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-2">
                    <table >
                    <thead>
                    <tr>
                                <td>Ticker</td>
                                <td>Position</td>
                                <td>Total sell quantity</td>
                                <td>Total buy quantity</td>
                                <td>Realised gain</td>
                                <td>Unreaslised gain</td>
                            </tr>
                    </thead>
                    @foreach($results as $key=>$result)
                            <tr>
                                <td>{{ $key }}</td>
                                @if ($result['position'] == "long")
                                    <td class="long">{{ $result['position'] }}</td>
                                @else
                                     <td class="short">{{ $result['position'] }}</td>
                                @endif
                                <td>{{ $result['sellQuantity'] }}</td>
                                <td>{{ $result['buyQuantity'] }}</td>
                                @if ($result['realisedGain']>0)
                                    <td class="profit">{{ $result['realisedGain'] }}</td>
                                @else @if ($result['realisedGain']==0)
                                    <td class="">{{ $result['realisedGain'] }}</td>
                                @else
                                     <td class="loss">{{ $result['realisedGain'] }}</td>
                                    @endif
                                @endif
                                @if ($result['unRealisedGain']>0)
                                    <td class="profit">{{ $result['unRealisedGain'] }}</td>
                                @else @if ($result['unRealisedGain']==0)
                                    <td class="">{{ $result['unRealisedGain'] }}</td>
                                @else
                                     <td class="loss">{{ $result['unRealisedGain'] }}</td>
                                 @endif
                                @endif
                            </tr>
                    @endforeach
                    <tr>
                        <td colspan="4">Total gain</td>
                        @if ($realisedGain>0)
                            <td class="profit">{{ $realisedGain }}</td>
                        @else @if ($realisedGain==0)
                            <td class="">{{ $realisedGain }}</td>
                        @else
                                <td class="loss">{{ $realisedGain }}</td>
                            @endif
                        @endif
                        @if ($unRealisedGain>0)
                            <td class="profit">{{ $unRealisedGain }}</td>
                        @else @if ($unRealisedGain==0)
                            <td class="">{{ $unRealisedGain }}</td>
                        @else
                                <td class="loss">{{ $unRealisedGain }}</td>
                            @endif
                        @endif
                    </tr>
                    </table>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
