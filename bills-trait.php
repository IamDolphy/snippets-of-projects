<?php

trait BillsTrait 
{
    /*
     * Calendar items with the same relationship seem to not return more than one
     * so have to manual add relationship
     */
    protected function assembleBills($model) 
    {
        return $model
        ->map(function($c) use($model){
            $day = date('j', strtotime($c->event_date));
            $display_date = Carbon::parse("{$this->month}/{$day}/{$this->year}")->format('D - j');
            $date = Carbon::parse("{$this->month}/{$day}/{$this->year}")->format('j/n/Y');
            $recurring = (!$c->inactive && $c->recurring);
            $event = $c;
            $bill = $model->where('morph_id', $c->morph_id)->pluck('bills')->first()->first();
            return array(
                $date, 
                null, 
                "javascript: calendarDrilldown('$display_date');", 
                $this->color( $date , date('j/n/Y') ),
                null,
                view('user.dashboard-partials.calendar-items.bills', compact('bill', 'recurring', 'event'))->render()
              );
        });
    }
    
    
    public function presentBill(Request $request, $id)
    {
        $date =  $request->get('date', '');
        
        $bill = Bills::where('bill_id', $id)
        ->where('family_id', User::currentFamily())
        ->with([
        'user', 
        'payments' => function($q) use($date){
            return $q
            ->whereDate('date_paid', '<=', $date)
            ->take(3);    
        },
        'payments.user'])->get()->first();
        $view = view('user.dashboard-partials.calendar-items.present-bill', compact('bill'))->render();
        return response()->json(compact('view'));
    }
    
}



?>
