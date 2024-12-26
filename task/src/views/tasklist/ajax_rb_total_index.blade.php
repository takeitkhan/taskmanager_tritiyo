<table class="mt-2 table is-bordered xis-striped is-narrow is-fullwidth"
       style="text-align: right;display: table; background: transparent; float: right">
    <tr>
        <td style="width: 10%"></td>
        @if(auth()->user()->isResource(auth()->user()->id))

        @else
        <td style="width: 45%" title="Requisition">R</td>
        @endif
        <td title="Bill">B</td>
    </tr>
    <tr>
        <td style="width: 10%" title="Resource">R</td>
        @if(auth()->user()->isResource(auth()->user()->id))

        @else
            <td></td>
        @endif
        <td title="Bill Submit By Resource">
            {{$calculate->bpbr_amount ?? 0 }}
        </td>
    </tr>
    <tr>
        <td title="Manager">M</td>
        @if(auth()->user()->isResource(auth()->user()->id))

        @else
            <td title="Requisition By Manager">
                {{$calculate->rpbm_amount ?? 0 }}
            </td>
        @endif
        <td title="Bill Edited By Manager">
            {{$calculate->bebm_amount ?? 0 }}
        </td>
    </tr>
    <tr>
        <td title="CFO">C</td>
        @if(auth()->user()->isResource(auth()->user()->id))

        @else
            <td title="Requisition By CFO">
                {{$calculate->rebc_amount ?? 0 }}
            </td>
        @endif
        <td title="Bill Edited By CFO">
            {{$calculate->bebc_amount ?? 0 }}
        </td>
    </tr>
    <tr>
        <td title="Accountant">A</td>
        @if(auth()->user()->isResource(auth()->user()->id))

        @else
            <td title="Requisition By Accountant">
                {{$calculate->reba_amount ?? 0 }}
            </td>
        @endif
        <td title="Bill Edited By Accountant">
            {{$calculate->beba_amount ?? 0 }}
        </td>
    </tr>
</table>
