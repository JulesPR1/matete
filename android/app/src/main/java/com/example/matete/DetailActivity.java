package com.example.matete;

import androidx.appcompat.app.AppCompatActivity;

import android.content.Intent;
import android.graphics.Color;
import android.media.Image;
import android.os.Bundle;
import android.provider.CalendarContract;
import android.util.Log;
import android.view.View;
import android.widget.Button;
import android.widget.ImageView;
import android.widget.TextView;

import com.example.matete.Model.Annonce;
import com.example.matete.Model.Categorie;
import com.example.matete.Model.Emplacement;
import com.example.matete.Model.MarkerDataTag;
import com.example.matete.Model.Vendeur;
import com.squareup.picasso.Picasso;

public class DetailActivity extends AppCompatActivity {

    private Annonce annonce;
    private Vendeur vendeur;
    private Categorie categorie;
    private Emplacement emplacement;
    private String ville;
    private Boolean isFavorite = false;

    private TextView nomAnnonce;
    private TextView description;
    private TextView localisationProduit;
    private TextView numVendeur;
    private TextView mailVendeur;
    private TextView ContactLBL;
    private TextView nomCateg;
    private TextView quantite;
    private ImageView imageAnnonce;

    private Button btnFavoris;
    private Button btnRetour;

    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);
        setContentView(R.layout.activity_detail);

        //on link les textview
        nomAnnonce = findViewById(R.id.nomAnnonce);
        description = findViewById(R.id.description);
        localisationProduit = findViewById(R.id.localisationProduit);
        numVendeur = findViewById(R.id.numVendeur);
        ContactLBL = findViewById(R.id.ContactLBL);
        nomCateg = findViewById(R.id.nomCateg);
        mailVendeur = findViewById(R.id.mailVendeur);
        imageAnnonce = findViewById(R.id.imageAnnonce);
        quantite = findViewById(R.id.quantité);

        //on prend les data du marker cliqué
        Intent intent = getIntent();
        annonce = (Annonce)intent.getParcelableExtra("Annonce");
        vendeur = (Vendeur)intent.getParcelableExtra("Vendeur");
        categorie = (Categorie) intent.getParcelableExtra("Categorie");
        emplacement = (Emplacement) intent.getParcelableExtra("Emplacement");
        ville = intent.getStringExtra("Ville");
        isFavorite = intent.getBooleanExtra("isFavorite", false);


        //reconstitution de l'annonce cliquée
        //Annonce annonceCliquée = new Annonce(annonce.getId(), annonce.getNom(), annonce.getDescription(), annonce.getQuantite(), annonce.getImage(), vendeur, categorie, emplacement);

        nomAnnonce.setText(annonce.getNom());
        description.setText(annonce.getDescription());
        localisationProduit.setText(emplacement.getAdresse() + ", " + ville + ", " + emplacement.getCodePostale());
        ContactLBL.setText("Contacter " + vendeur.getNom() + ":");
        nomCateg.setText(categorie.getLibelle());
        mailVendeur.setText("mail : " + vendeur.getMail());
        numVendeur.setText("numéro : " + vendeur.getNumero());
        quantite.setText(annonce.getQuantite());

        Picasso.get().load(annonce.getImage()).into(imageAnnonce);

        btnFavoris = findViewById(R.id.favoris);
        btnRetour = findViewById(R.id.fermer);

        if(isFavorite){
            btnFavoris.setText("Retirer des favoris");
        }

        // Nous paramétrons un écouteur sur l’événement ‘click’ du bouton ajouter aux favoris
        btnFavoris.setOnClickListener(new View.OnClickListener()
           {
               public void onClick(View v)
               {
                   Intent intent = new Intent();
                   if(!isFavorite) {
                       intent.putExtra("idAnnonceFavoris", annonce.getId()); //pass intent extra here
                   }else{
                       intent.putExtra("idAnnonceFavoris", annonce.getId());
                       intent.putExtra("canceled?", true);
                   }
                   setResult(RESULT_OK,intent);
                   finish();
               }
           }
        );

        // Nous paramétrons un écouteur sur l’événement ‘click’ du bouton retour
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
        }
        );
    }
}