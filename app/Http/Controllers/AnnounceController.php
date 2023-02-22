<?php

namespace App\Http\Controllers;

use App\Models\Announce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnnounceController extends Controller
{
    public function index()
    {
        $post = DB::table('announces')
            ->get();
        return view('announce.index', compact('post'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        Announce::create($input);
        return redirect()->route('announces.index')->with('success', 'สร้างประกาศของลูกค้าเรียบร้อยแล้ว');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('announce.create');
    }
    public function destroy(Announce $announce)
    {
        $announce->delete();
        return redirect()->route('announces.index')->with('success', 'ลบประกาศของลูกค้าเรียบร้อยแล้ว');
    }
}
