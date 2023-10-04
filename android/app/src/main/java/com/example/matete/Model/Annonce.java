package com.example.matete.Model;

import android.os.Parcel;
import android.os.Parcelable;

public class Annonce implements Parcelable {
    private String id;
    private String nom;
    private String description;
    private String quantite;
    private Vendeur vendeur;
    private String image;
    private Categorie categorie;
    private Emplacement emplacement;

    public Annonce(String id, String nom, String description, String quantite,String image, Vendeur vendeur, Categorie categorie, Emplacement emplacement) {
        this.id = id;
        this.nom = nom;
        this.description = description;
        this.quantite = quantite;
        this.image = image;
        this.vendeur = vendeur;
        this.categorie = categorie;
        this.emplacement = emplacement;
    }

    public String getId() {
        return id;
    }

    public String getNom() {
        return nom;
    }

    public String getDescription() {
        return description;
    }

    public String getQuantite() {
        return quantite;
    }

    public String getImage() { return image; }

    public Vendeur getVendeur() {
        return vendeur;
    }

    public Categorie getCategorie() {
        return categorie;
    }

    public Emplacement getEmplacement() {
        return emplacement;
    }

    ////pour les rendre parcelable
    public int describeContents() {
        return 0;
    }

    public void writeToParcel(Parcel out, int flags) {
        out.writeString(id);
        out.writeString(nom);
        out.writeString(description);
        out.writeString(quantite);
        out.writeString(image);
    }

    public static final Parcelable.Creator<Annonce> CREATOR
            = new Parcelable.Creator<Annonce>() {
        public Annonce createFromParcel(Parcel in) {
            return new Annonce(in);
        }

        public Annonce[] newArray(int size) {
            return new Annonce[size];
        }
    };

    private Annonce(Parcel in) {
        id = in.readString();
        nom = in.readString();
        description = in.readString();
        quantite = in.readString();
        image = in.readString();
    }
}
