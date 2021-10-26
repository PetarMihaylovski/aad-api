<!doctype html>
<html lang="en">
<head>
    <style>
        table {
            font-family: arial, sans-serif;
            border-collapse: collapse;
            width: 100%;
        }

        td, th {
            border: 1px solid #dddddd;
            text-align: left;
            padding: 8px;
        }

        tr:nth-child(even) {
            background-color: #dddddd;
        }
    </style>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Test Mail</title>
</head>
<body>
    <h1>{{$details['title']}}</h1>

    <table>
        <tr>
            <th>Product</th>
            <th>price</th>
            <th>amount</th>
        </tr>
        <tr>
            <td>{{$details['product']['name']}}</td>
            <td>€{{$details['product']['price']}}</td>
            <td>{{$details['product']['stock']}}</td>
            <td>Total :€{{$details['total']}}</td>
        </tr>
    </table>

</body>
</html>