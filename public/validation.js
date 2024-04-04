



















function myFunction()
{
   // location.reload();
    
    var table=$('#sample_data').DataTable();
    /////supprimer les modifications si elles existent
   /* table.rows().every(function() {
        var rowNode = this.node();
       $(rowNode).removeClass('blink-background');
    })*/
   
    ////////////////////////////////////////////////////////////////////////////////////////////////////////
// Parcourir chaque ligne du DataTable
table.rows().every(function() {
    var rowData = this.data(); // Données de la ligne actuelle
    var rowNode = this.node();
    // Faites votre test sur la valeur de la cellule
    if (rowData['code'] === 'EFA.11') {
        var table1=$('#sample_data').DataTable();
        var var1=var2=var3=0;
        var rowNodeVar1,rowNodeVar2,rowNodeVar3,test;
// chercher les autres variables de test
     table1.rows().every(function() {
    var rowDataX = this.data(); // Données de la ligne actuelle
    var rowNodeVarX = this.node();
    if (rowDataX['code'] === 'EFA.11.01.06') {
        //var rowDataVar1 = this.data(); // Données de la ligne actuelle
        rowNodeVar1 = rowNodeVarX;
        var1=rowDataX['value_n'];        
    }
    if (rowDataX['code'] === 'EFA.01.01.01.10') {
        rowNodeVar2 = rowNodeVarX;
        var2=rowDataX['value_n'];
    }
    if (rowDataX['code'] === 'EFA.11.01.03') {
        rowNodeVar3 =  rowNodeVarX;
        var3=rowDataX['value_n'];
    
    }
        
       })
     
      if((parseFloat(var1)+parseFloat(var2)+parseFloat(var3))!= rowData['value_n'])
      {
        alert("EFA.11 = EFA.11.01.06 + EFA.01.01.01.10 + EFA.11.01.03");
        $(rowNode).addClass('green-background');
        if(rowNodeVar1!=undefined)
       { $(rowNodeVar1).addClass('blink-background');}
       if(rowNodeVar2!=undefined)
        {$(rowNodeVar2).addClass('blink-background');}
        if(rowNodeVar3!=undefined)
        {$(rowNodeVar3).addClass('blink-background');}
    }
   else{
        console.log(rowData['value_n']);
    }
    }
});




}