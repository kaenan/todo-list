<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Lists;
use App\Models\ListItem;
use Illuminate\Http\Request;
use Illuminate\Support\Js;
use stdClass;

class TodoListController extends Controller
{
    public function index() {
        // return view('welcome', ['listItems' => ListItem::all()]);

        $lists_all = Lists::where('archived', 0)->get();
        $lists = [];
        foreach ($lists_all as $l) {
            $list = new stdClass();
            $list->id = $l->id;
            $list->name = $l->name;
            // $list->items = ListItem::where('listid', $l->id)->get();
            $list_items_records = ListItem::where('listid', $l->id)->get();
            $list_items = [];

            foreach ($list_items_records as $item) {
                $i = new stdClass();
                $i->id = $item->id;
                $i->name = $item->name;
                $i->completed = $item->complete ? 'checked' : '';
                $list_items[] = $i;
            }

            $list->items = $list_items;

            $lists[] = $list;
        }

        return view('welcome', ['lists' => $lists]);
    }

    public function createList(Request $request) {

        $newList = new Lists;
        $newList->name = $request->list_name;
        $newList->save();

        return redirect('/');
    }

    public function saveItem(Request $request) {

        $name = $request->name;

        if (!$name || trim($name) == null){
            return response()->json();
        }
        
        $newItem = new ListItem;
        $newItem->listid = $request->listid;
        $newItem->name = $request->name;
        $newItem->complete = 0;
        $newItem->save();

        $list_items = ListItem::where('listid', $request->listid)->get();

        return response()->json([$list_items]);
    }

    public function markComplete(Request $request) {

        $item = ListItem::find($request->id);
        $item->complete = $request->complete;
        $item->save();

        return response()->json();
    }

    public function archiveList(Request $request) {

        $list = Lists::find($request->listid);
        $list->archived = 1;
        $list->save();

        return response()->json();
    }

    public function deleteItem(Request $request) {

        $success = ListItem::where('id', $request->itemid)->delete();

        return response()->json();
    }
}
