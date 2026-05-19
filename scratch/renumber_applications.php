<?php
// Renumber all existing hostel applications to LUC-NGA-002-ADM-XXXXXXX format
$apps = App\Models\HostelApplication::orderBy('id')->get();
foreach ($apps as $i => $app) {
    $num = 1000000 + ($i + 1);
    $newId = 'LUC-NGA-002-ADM-' . str_pad($num, 7, '0', STR_PAD_LEFT);
    $app->update(['application_number' => $newId]);
}
$total = App\Models\HostelApplication::count();
$sample = App\Models\HostelApplication::first()->application_number;
echo "Done! Total: $total | First: $sample\n";
