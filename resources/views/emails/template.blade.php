<table>
    <tbody>
        <tr>
            <center><h3>{!! $data["salute"] !!}</h3></center>
            <p>{!! $data["message"] !!}</p>
            @if(isset($data['targetUrl']))
                <center><a href="{!! $data['targetUrl'] !!}">{!! $data["buttonTitle"] !!}</a></center>
            @endif
        </tr>
    </tbody>
</table>