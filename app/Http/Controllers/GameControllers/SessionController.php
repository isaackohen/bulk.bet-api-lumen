<?php

namespace App\Http\Controllers\GameControllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests; 
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Specialtactics\L5Api\Http\Controllers\RestfulController as BaseController;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Http;
use Illuminate\Http\Request;
use \App\Models\Gameoptions;
use Dingo\Api\Routing\Helpers;
use Dingo\Api\Http\Response;


class SessionController extends Controller
{
    use Helpers;

            public function createSession(Request $request)
            {
                $apikey = $request['apikey'];
                $game = $request['game'];
                $userid = $request['userid'];
                $mode = $request['mode'] ?? 'real';
                $name = $request['name'] ?? $userid;
                $fs = $request['freespins'] ?? 0;
                $fsamount = $request['freespins_value'] ?? '0.20';


                $apikey_get = DB::table('gameoptions')
                ->where('apikey', '=', $apikey )
                ->first();

                if(!$apikey_get) {
                return Response(array('status' => 'error', 'error' => 'Auth error (1)'))->setStatusCode(503);
                }

                $findoperator = DB::table('gameoptions')
                ->where('apikey', '=', $apikey)
                ->first();

                if(!$findoperator) {
                return Response(array('status' => 'error', 'error' => 'Auth error (2: casino level)'))->setStatusCode(401);
                }

                $gameid = DB::table('gamelist')
                ->where('game_id', '=', $game)
                ->first();

                if(!$gameid) {
                return Response(array('status' => 'error', 'error' => 'Game not found'))->setStatusCode(404);
                }

                if($gameid->type === "live" and $findoperator->livecasino_enabled == '0') {
                return Response(array('error' => 'Livecasino not enabled on your account'))->setStatusCode(401);
                }


                $get_casinoid = $findoperator->id;
                $get_gameid = $gameid->game_id;





                /** @param create Free Spins Session on Evoplay */
                    if($mode === "bonus" and $fs > 0) {
                        $url = 'https://rpc.bet/freespins/'.$userid.'/'.$get_gameid.'/'.$get_casinoid.'/'.$fs.'/'.$fsamount.'/';
                        $response = Http::get($url);
                        $return = $response['url'];
                        return array('url' => $return);
                    }

                /** @param create Regular Session on Evoplay */
                    if($gameid->game_provider === "evoplay") {
                        $url = 'https://rpc.bet/session/'.$mode.'/evoplay/'.$userid.'/'.$get_gameid.'/'.$get_casinoid.'/'.$mode.'/';
                        $response = Http::get($url);
                        $return = $response['url'];

                        return array('url' => $return);
                    }

                /** @param create Regular Session on Mascot Gaming */
                    if($gameid->api_ext === "c2" and $gameid->game_provider === "mascot") {
                        $url = 'https://rpc.bet/session/'.$mode.'/mascot/'.$userid.'/'.$get_gameid.'/'.$get_casinoid.'/'.$findoperator->bankgroup.'/'.$findoperator->statichost.'/';
                        $response = Http::get($url);
                        $return = $response['url'];

                        return array('url' => $return);
                    }

                /** @param create Regular Session on C2 Gaming */
                    if($gameid->api_ext === "c2"  and $gameid->type === "slots") {
                        if($mode === 'demo') {
                        $url = 'https://api.bulk.bet/session/'.$mode.'/c2/'.$get_gameid;

                        } else {
                        $url = 'https://api.bulk.bet/session/'.$mode.'/c2/'.$userid.'/'.$get_gameid.'/'.$get_casinoid.'/'.$findoperator->bankgroup.'/'.$findoperator->statichost.'/';
                        }

                        $response = Http::get($url);
                        $return = $response['url'];

                        return array('url' => $return);
                    }

                /** @param create Regular Session on Evoplay */
                    if($gameid->api_ext === "rise" and $gameid->type === "live") {
                        if($mode === 'demo'){
                                return array('url' => 'https://bulk.bet/errorpage/404_demo/');
                        }
                    return RiseController::createLive($gameid->game_provider, $gameid->extra_id, $userid, $get_casinoid, $name);
                    }

                    if($gameid->api_ext === "rise" and $gameid->type === "slots") {
                        if($mode === 'demo'){
                                return array('url' => 'https://bulk.bet/errorpage/404_demo/');
                        }
                        return RiseController::createSlots($gameid->game_provider, $get_gameid, $userid, $get_casinoid, $name);
                    }

            }
}