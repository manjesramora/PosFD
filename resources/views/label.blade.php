<!DOCTYPE html>
<html>

<head>
    <title>Etiqueta de Código de Barras</title>
    <style>
        body {
            margin: 0;
            padding: 0;
        }

        .label-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            border: 1px solid #000;
            padding: 5px;
            box-sizing: border-box;
            width: 6cm;
            height: 3cm;
            margin: 5px;
            page-break-after: always;
        }

        .description {
            text-align: center;
            font-size: 12px;
            margin-bottom: 5px;
        }

        .barcode {
            display: flex;
            justify-content: center;
            margin-bottom: 5px;
        }

        .sku {
            font-size: 10px;
            text-align: center;
        }
    </style>
</head>

<body>
    @foreach ($labels as $label)
    <div class="label-container">
        <div class="description">
            {{ $label['description'] }}
        </div>
        <div class="barcode">
            {!! $label['barcode'] !!}
        </div>
        <div class="sku">
            SKU: {{ $label['sku'] }}
        </div>
    </div>
    @endforeach
</body>

</html>