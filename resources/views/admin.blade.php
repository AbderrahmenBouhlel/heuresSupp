<!-- resources/views/admin.blade.php -->
 <!DOCTYPE html>
 <html lang="en">
 <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
 </head>
 <body>
   <h1>Admin page</h1>
   <p>This page is rendered using Blade.</p>


   <p>Nom: {{ $adminVar->nom }}</p>
   <p>Prénom: {{ $adminVar->prenom }}</p>
   <p>Email: {{ $adminVar->email }}</p>
   <p>Département: {{ $adminVar->departement_principal }}</p>
   <p>Actif depuis: {{ $adminVar->active_from }}</p>
    
 </body>
 </html>