<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Memo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        /*
         * Custom CSS to handle the specifics of the receipt design
         * These styles are crucial for a 100% match.
         */
        body {
            /* The font appears to be a standard serif, similar to Times New Roman.
             * For a 100% match, you should identify and use the exact font.
             */
            font-family: 'Times New Roman', serif;
            -webkit-print-color-adjust: exact;
            color-adjust: exact;
        }

        /* Container for the overall receipt content */
        .receipt-container {
            width: 210mm; /* A4 paper width */
            padding: 30mm 20mm; /* Adjust padding to match the margins of the physical receipt */
            box-sizing: border-box;
            line-height: 1.25;
        }

        /* Specific styling for the top-right code box */
        .code-box {
            border: 1px solid black;
            padding: 0.25rem 0.75rem;
        }

        /* Styling for the bank account number grid at the bottom */
        .account-grid {
            display: flex;
            gap: 1px; /* Small gap between the boxes */
        }
        .account-box {
            width: 32px; /* Adjust size to match the squares on the receipt */
            height: 32px;
            border: 1px solid black;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.1rem;
        }

        /* Print-specific styles to remove shadows or backgrounds if needed */
        @media print {
            body {
                background: white;
            }
        }
    </style>
</head>
<body class="bg-gray-100 flex justify-center">

    <div class="receipt-container bg-white text-black text-sm mx-auto shadow-lg">
        
        <header class="grid grid-cols-2 gap-4 items-start mb-10">
            <div class="flex flex-col items-start space-y-2">
                <div class="text-center w-full">
                    <img src="https://placehold.co/80x80/e0e0e0/000?text=MINISTRY" alt="Ministry Logo" class="mx-auto mb-2">
                    <p class="font-bold text-xs leading-none">Ministère de l'Enseignement Supérieur<br>et de la Recherche Scientifique</p>
                    <p class="text-[0.6rem] leading-none">**********</p>
                </div>
                <div class="text-center w-full">
                    <p class="font-bold text-sm leading-none">Université de Sousse</p>
                    <p class="text-[0.6rem] leading-none">**********</p>
                    <p class="text-xs leading-none">Institut Supérieur des Sciences Appliquées<br>et de Technologie de Sousse</p>
                </div>
            </div>

            <div class="flex flex-col items-end space-y-6">
                <img src="https://placehold.co/150x50/e0e0e0/000?text=issat" alt="ISSAT Logo" class="mt-4">
                <div class="code-box text-sm">
                    <p>Code FR-AP-15</p>
                    <hr class="border-black my-1">
                    <p>Révision :01</p>
                    <hr class="border-black my-1">
                    <p>Date : 14/11/2024</p>
                </div>
            </div>
        </header>

        <div class="text-center mb-10">
            <h1 class="text-lg font-bold">Mémoire de Paiement des Heures Complémentaires</h1>
            <h2 class="text-lg font-bold">(Enseignant Permanent)</h2>
            <p class="text-lg font-bold">2020-2021</p>
        </div>

        <section class="mb-6 space-y-3 pl-20">
            <p><span class="inline-block w-40">Nom Et Prénom de l'Enseignant:</span> 
               <span class="font-bold ml-10">{{ NOM_ET_PRENOM }}</span></p>
            <p><span class="inline-block w-40">Grade:</span> 
               <span class="ml-10">Maître de Conférences</span></p>
            <p><span class="inline-block w-40">Coef. Kilométrique(Pris en Consideration):</span> 
               <span class="ml-10">1</span></p>
        </section>

        <section class="flex flex-col items-center">
            <table class="w-[80%] border border-black mb-4">
                <thead>
                    <tr class="text-center border-b border-black">
                        <th class="py-1 px-2 border-r border-black font-bold"></th>
                        <th class="py-1 px-2 border-r border-black font-bold">Cours</th>
                        <th class="py-1 px-2 border-r border-black font-bold">TD</th>
                        <th class="py-1 px-2 font-bold">TP</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="py-1 px-2 border-r border-black font-bold text-left">Horaire Complémentaire</td>
                        <td class="py-1 px-2 border-r border-black text-center">{{ HEURES_COURS }}</td>
                        <td class="py-1 px-2 border-r border-black text-center">{{ HEURES_TD }}</td>
                        <td class="py-1 px-2 text-center">{{ HEURES_TP }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 px-2 border-r border-black font-bold text-left">Taux Annuel des Heures Complémentaires</td>
                        <td class="py-1 px-2 border-r border-black text-center">{{ TAUX_COURS }}</td>
                        <td class="py-1 px-2 border-r border-black text-center">{{ TAUX_TD }}</td>
                        <td class="py-1 px-2 text-center">{{ TAUX_TP }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 px-2 border-r border-black font-bold text-left">Montant des Heures Complémentaires</td>
                        <td class="py-1 px-2 border-r border-black text-center">{{ MONTANT_COURS }}</td>
                        <td class="py-1 px-2 border-r border-black text-center">{{ MONTANT_TD }}</td>
                        <td class="py-1 px-2 text-center">{{ MONTANT_TP }}</td>
                    </tr>
                </tbody>
            </table>

            <table class="w-[80%] border border-black mb-10">
                <tbody>
                    <tr>
                        <td class="py-1 px-2 border-r border-black text-left font-bold w-[75%]">Total (Brut)</td>
                        <td class="py-1 px-2 text-right font-bold">{{ TOTAL_BRUT }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 px-2 border-r border-black text-left font-bold w-[75%]">Impôts</td>
                        <td class="py-1 px-2 text-right font-bold text-blue-700">{{ IMPOTS }}</td>
                    </tr>
                    <tr>
                        <td class="py-1 px-2 border-r border-black text-left font-bold w-[75%]">Net à Payer</td>
                        <td class="py-1 px-2 text-right font-bold">{{ NET_A_PAYER }}</td>
                    </tr>
                </tbody>
            </table>
        </section>

        <section class="px-20">
            <p class="font-bold mb-4">Arrêté à la Somme de:</p>
            <p class="mb-6 indent-8">{{ MONTANT_EN_LETTRES }}</p>

            <p class="mb-2">A virer au Compte N° (à remplir par l'Enseignant, sous sa RESPONSABILITE) :</p>
            <div class="account-grid mb-12">
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
                <div class="account-box"></div>
            </div>

            <div class="flex justify-between items-center mb-10">
                <div class="text-center leading-tight">
                    <p class="font-bold">Signature du Bénéficiaire</p>
                </div>
                <div class="text-center leading-tight">
                    <p>Le Directeur de l'ISSAT Sousse</p>
                    <p class="font-bold mt-1">Prof. Sami CHATTI</p>
                </div>
            </div>

            <p class="text-center font-bold">Editeé le "{{ DATE_EDITION }}"</p>
        </section>
    </div>
</body>
</html>