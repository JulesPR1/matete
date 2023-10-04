package com.example.matete;

import androidx.annotation.NonNull;
import androidx.appcompat.app.AppCompatActivity;
import androidx.recyclerview.widget.LinearLayoutManager;
import androidx.recyclerview.widget.RecyclerView;

import android.content.Intent;
import android.os.Bundle;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.MotionEvent;
import android.view.View;
import android.widget.Button;
import android.widget.EditText;

import com.example.matete.Model.Annonce;
import com.example.matete.Model.Categorie;
import com.example.matete.Model.Emplacement;
import com.example.matete.Model.MarkerDataTag;
import com.example.matete.Model.Vendeur;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.model.Marker;

import java.util.ArrayList;
import java.util.Locale;

public class FavorisActivity extends AppCompatActivity {

    private RecyclerView recyclerView;
    private FavorisAdapter adapter;
    private ArrayList<MarkerDataTag> lesAnnoncesFavorites = new ArrayList();
    private ArrayList<MarkerDataTag> lesAnnoncesFavoritesRecherchées = new ArrayList();
    private ArrayList lesIdDesAnnoncesFav = new ArrayList();
    private EditText rechercheInput;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_favoris);
        Intent intent = getIntent();

        int index = intent.getIntExtra("nbAnnoncesFav", 0);

        for(int i = 0; i<index; i++) {
            Annonce annonce = intent.getParcelableExtra("Annonce" + i);
            lesIdDesAnnoncesFav.add(annonce.getId());
            Vendeur vendeur = intent.getParcelableExtra("Vendeur" + i);
            Categorie categorie = intent.getParcelableExtra("Categorie" + i);
            Emplacement emplacement = intent.getParcelableExtra("Emplacement" + i);
            String ville = intent.getStringExtra("Ville" + i);

            Annonce reconstistuedAnnonce = new Annonce(annonce.getId(), annonce.getNom(), annonce.getDescription(), annonce.getQuantite(), annonce.getImage(), vendeur, categorie, emplacement);
            //Convertit l'annonce crée en MarkerDataTag pour aussi récup la ville et autre données supplémentaires si besoin
            MarkerDataTag reconstituedMarkerDataTag = new MarkerDataTag(ville, reconstistuedAnnonce);

            Log.e("", lesIdDesAnnoncesFav.toString());

            lesAnnoncesFavorites.add(reconstituedMarkerDataTag);
            lesAnnoncesFavoritesRecherchées.add(reconstituedMarkerDataTag);
        }

        //bar de recherche
        rechercheInput = findViewById(R.id.recherche);
        rechercheInput.addTextChangedListener(new TextWatcher() {

            public void afterTextChanged(Editable s) {}

            public void beforeTextChanged(CharSequence s, int start, int count, int after) {}

            public void onTextChanged(CharSequence s, int start, int before, int count) {
                String recherche = rechercheInput.getText().toString();
                lesAnnoncesFavoritesRecherchées.clear();
                for (int i=0; i<lesAnnoncesFavorites.size(); i++){
                    MarkerDataTag currentAnnonce = lesAnnoncesFavorites.get(i);
                    if(currentAnnonce.getAnnonce().getNom().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))
                            || currentAnnonce.getAnnonce().getDescription().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))
                            || currentAnnonce.getAnnonce().getCategorie().getLibelle().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))
                            || currentAnnonce.getVille().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))){
                        lesAnnoncesFavoritesRecherchées.add(currentAnnonce);
                    }
                }
                adapter.notifyDataSetChanged();
            }
        });

        // set up the RecyclerView
        recyclerView = findViewById(R.id.listeFavoris);
        recyclerView.setLayoutManager(new LinearLayoutManager(this));
        adapter = new FavorisAdapter(this, lesAnnoncesFavoritesRecherchées);
        recyclerView.setAdapter(adapter);
        recyclerView.addOnItemTouchListener(
                new RecyclerItemClickListener(this, recyclerView ,new RecyclerItemClickListener.OnItemClickListener() {
                    @Override public void onItemClick(View view, int position) {
                        //ouverture page détail
                        Intent intent = new Intent(getApplicationContext(), DetailActivity.class);
                        MarkerDataTag annonceCliquée = lesAnnoncesFavoritesRecherchées.get(position);
                        intent.putExtra("Annonce", annonceCliquée.getAnnonce());
                        intent.putExtra("Vendeur", annonceCliquée.getAnnonce().getVendeur());
                        intent.putExtra("Categorie", annonceCliquée.getAnnonce().getCategorie());
                        intent.putExtra("Emplacement", annonceCliquée.getAnnonce().getEmplacement());
                        intent.putExtra("Ville", annonceCliquée.getVille());
                        intent.putExtra("isFavorite", true);
                        startActivity(intent);
                    }
                    @Override public void onLongItemClick(View view, int position) {
                        // do whatever
                    }
                })
        );


        // Nous paramétrons un écouteur sur l’événement ‘click’ du bouton retour
        Button btnRetour = findViewById(R.id.fermer);
        btnRetour.setOnClickListener(new View.OnClickListener()
         {
             public void onClick(View v)
             {
                 Intent intent = new Intent();
                 intent.putExtra("idAnnonceFavoris", "renvoieUnStringPourEviterDeCrash"); //pass intent extra here
                 intent.putExtra("canceled?", false);
                 setResult(RESULT_OK,intent);
                 finish();
             }
         });
    }
}