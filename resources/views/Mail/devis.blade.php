<!-- resources/views/Mail/facture.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Devis</title>
</head>
<body>
    <h1>Devis #{{ $devis->devis_number }}</h1>
    <p>Date d'échéance : {{ $devis->due_date }}</p>
    <p>Date d'échéance : {{ $devis->due_date }}</p>
    <p>Teply to : {{ $emailFrom }}</p>
    <p>Montant total (HT) : ${{ $devis->total_amount_ht }}</p>
    <p>TVA : ${{ $devis->total_vat }}</p>
    <p>Montant total (TTC) : ${{ $devis->total_amount_ttc }}</p>

    <h2>Détails des articles</h2>
    <ul>
        @foreach($devis->devis_line_items as $item)
            <li>
                <strong>{{ $item->title }}</strong><br>
                Description: {{ $item->description }}<br>
                Prix unitaire HT: {{ $item->unit_price_ht }}<br>
                TVA: {{ $item->vat }}%<br>
                Total HT: {{ $item->line_total_ht }}<br>
                Total TTC: {{ $item->unit_price_ttc }}
            </li>
        @endforeach
    </ul>
</body>
</html>
