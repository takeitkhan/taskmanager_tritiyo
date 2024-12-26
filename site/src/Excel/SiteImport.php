<?php

namespace Tritiyo\Site\Excel;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStartRow;

use Tritiyo\Site\Models\Site;

class SiteImport implements ToCollection, WithStartRow
{


    public function collection(Collection $row)
    {
      $row = $row->keyBy(function ($item) {
          return strtoupper($item[1]);
      });
    
      //dd($row->all());
      $row = $row->groupBy(1)->toArray();
      //dd($row->toArray());
      $row = $row;
		//dd($row);
        $matechedSite = [];
       $matechedSiteButNotPending = [];
        foreach ($row as $site) {
          	//dd($site[0][3]);
          	$site = $site[0];
          //dd($site[3]);
            $dbSites = \Tritiyo\Site\Models\Site::where('project_id', $site[3])->where('site_code', strtoupper($site[1]))->where('completion_status', 'Pending')->first();
//            dd($dbSites);
            if (!empty($dbSites) && $dbSites['site_code'] == strtoupper($site[1])) {
                ##$getManager = \Tritiyo\Project\Models\Project::where('id', $request->project_id)->first()->manager;
              	$pm = \Tritiyo\Project\Models\Project::where('id', $site[3])->first()->manager ?? NULL;
              	$siteLimit = $pm == '126' ? '10' : '5';
                $matechedSite[] = [
                    'serial' => $site[0],
                    'site_code' => strtoupper($site[1]),
                    'location' => $site[2],
                    'project_id' => $site[3],
                    'pm' => $pm,
                    'completion_status' => $dbSites['completion_status'],
                    'pending_note' => $dbSites['pending_note'],
                    'site_id' => $dbSites['id'],
                    'task_limit' => $site[4] ?? $siteLimit,
                    //'range_ids' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($site[3]),
                ];
            } else {
              if($site[1]){
                 $newSites = \Tritiyo\Site\Models\Site::where('site_code', strtoupper($site[1]))->where('project_id', $site[3])->first();
                if(!empty($newSites)){
                  $matechedSiteButNotPending[] =
                  [
                      'site_code' => $site[1],
                      //'site_code' => NULL,
                      'message' =>  $site[1].' is already imported but completion status not as pending',
                      //'message' =>  NULL,
                  ];
                }
              }
            }
        }
        //dd($matechedSite);
        session()->get('sitematched');
        session()->put('sitematched', $matechedSite);
      
      	session()->get('matechedSiteButNotPending');
        session()->put('matechedSiteButNotPending', $matechedSiteButNotPending);
      	

        //CheckunMatched
        $unMatechedSite = [];
        foreach ($row as $site) {
          	$site = $site[0];
            $dbSites = \Tritiyo\Site\Models\Site::where('project_id', $site[3])->where('site_code', strtoupper($site[1]))->first();
          //dump($dbSites);
            if (!empty($dbSites) && ($dbSites['site_code'] == strtoupper($site[1]) && $dbSites['project_id'] == $site[3])) {

            } else {
              	$pm = \Tritiyo\Project\Models\Project::where('id', $site[3])->first()->manager ?? NULL;
              	$siteLimit = $pm == '126' ? '10' : '5';
              	if($site[1]){
                  $unMatechedSite[] = [
                      'serial' => $site[0],
                      'site_code' => strtoupper($site[1]),
                      'location' => $site[2],
                      'project_id' => $site[3],
                      'pm' => $pm,
                      'task_limit' => $site[4] ?? $siteLimit,
                      //'range_ids' => \Tritiyo\Project\Helpers\ProjectHelper::current_range_id($site[3]),
                  ];
                }
            }
        }
        //dd($unMatechedSite);
        session()->get('siteunmatched');
        session()->put('siteunmatched', $unMatechedSite);
		//exit();
    }


    public function startRow(): int
    {
        return 2;
    }
}
