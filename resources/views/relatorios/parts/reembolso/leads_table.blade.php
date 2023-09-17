<table class="table table-striped table-hover order-column datatables" >
    <tbody>
        <tr>
            <th> Nome completo</th>
            <th> Nome </th>
            <th> Sobrenome </th>
            <th> E-mail </th>
            <th> Telefone </th>
            <th class="text-center"> CEP </th>
            <th> Data de cadastro </th>
            <th> Data de expiração </th>
        </tr>
        @foreach($leads as $lead)
            <tr class="even gradeX">
                <td> {{ $lead->nome . ' ' . $lead->sobrenome }} </td>
                <td> {{ $lead->nome }} </td>
                <td> {{ $lead->sobrenome }} </td>
                <td> {{ $lead->email }} </td>
                <td> {{ $lead->telefone }} </td>
                <td> {{ $lead->cep }} </td>
                <td> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lead->created_at)->format('d/m/Y H:i') }} </td>
                <td> {{ Carbon\Carbon::createFromFormat('Y-m-d H:i:s', $lead->data_expiracao)->format('d/m/Y H:i') }} </td>
            </tr>
        @endforeach

    </tbody>
</table>
