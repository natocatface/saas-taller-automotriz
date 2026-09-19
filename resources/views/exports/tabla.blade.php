<meta charset="UTF-8">
<table border="1" cellspacing="0" cellpadding="6">
    <thead>
        <tr style="background:#0d9488; color:#ffffff; font-weight:bold;">
            @foreach ($columnas as $c)
                <th>{{ $c }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($filas as $fila)
            <tr>
                @foreach ($fila as $cell)
                    <td>{{ $cell }}</td>
                @endforeach
            </tr>
        @endforeach
    </tbody>
</table>
