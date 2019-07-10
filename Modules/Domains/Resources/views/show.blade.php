    <table class='table table-striped'>
        <thead>
        </thead>
        <tbody>
            <tr>
                <td class="table-title"><b>Domínio:</b></td>
                <td>{!! $domain->name !!}</td>
            </tr>
            <tr>
                <td class="table-title"><b>IP que o domínio aponta:</b></td>
                <td>{!! $domain->domain_ip !!}</td>
            </tr>

            @foreach ($zones as $zone)
                @if($zone->name == $domain->name)
                    @foreach ($zone->name_servers as $new_name_server)
                        <tr>
                            <td class="table-title"><b>Novo servidor DNS :</b></td>
                            <td> {!! $new_name_server !!}</td>
                        </tr>
                    @endforeach
                @endif
            @endforeach
        </tbody>
    </table>

