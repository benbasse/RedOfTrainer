<!-- resources/views/Mail/facture.blade.php -->
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture</title>
</head>
<body>
    <h1>Facture #{{ $facture->facture_number }}</h1>
    <p>Date d'échéance : {{ $facture->due_date }}</p>
    <p>Montant total (HT) : ${{ $facture->total_amount_ht }}</p>
    <p>TVA : ${{ $facture->total_vat }}</p>
    <p>Montant total (TTC) : ${{ $facture->total_amount_ttc }}</p>
    <p>Reply to : ${{ $emailFrom }}</p>

    <h2>Détails des articles</h2>
    <ul>
        @foreach($facture->line_items as $item)
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
