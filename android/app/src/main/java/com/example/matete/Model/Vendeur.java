package com.example.matete.Model;

import android.os.Parcel;
import android.os.Parcelable;

public class Vendeur implements Parcelable{
    private String id;
    private String nom;
    private String numero;
    private String mail;

    public Vendeur(String id, String nom, String numero, String mail) {
        this.id = id;
        this.nom = nom;
        this.numero = numero;
        this.mail = mail;
    }

    public String getId() {
        return id;
    }

    public String getNom() {
        return nom;
    }

    public String getNumero() {
        return numero;
    }

    public String getMail() {
        return mail;
    }

    ////pour les rendre parcelable
    public int describeContents() {
        return 0;
    }

    public void writeToParcel(Parcel out, int flags) {
        out.writeString(id);
        out.writeString(nom);
        out.writeString(numero);
        out.writeString(mail);
    }

    public static final Parcelable.Creator<Vendeur> CREATOR
            = new Parcelable.Creator<Vendeur>() {
        public Vendeur createFromParcel(Parcel in) {
            return new Vendeur(in);
        }

        public Vendeur[] newArray(int size) {
            return new Vendeur[size];
        }
    };

    private Vendeur(Parcel in) {
        id = in.readString();
        nom = in.readString();
        numero = in.readString();
        mail = in.readString();
    }
}
