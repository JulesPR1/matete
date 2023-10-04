package com.example.matete.Model;

import android.os.Parcel;
import android.os.Parcelable;

public class Categorie implements Parcelable {
    private String libelle;

    public Categorie(String libelle) {
        this.libelle = libelle;
    }

    public String getLibelle() {
        return libelle;
    }

    @Override
    public String toString() {
        return libelle;
    }

    @Override
    ////pour les rendre parcelable
    public int describeContents() {
        return 0;
    }

    public void writeToParcel(Parcel out, int flags) {
        out.writeString(libelle);
    }

    public static final Parcelable.Creator<Categorie> CREATOR
            = new Parcelable.Creator<Categorie>() {
        public Categorie createFromParcel(Parcel in) {
            return new Categorie(in);
        }

        public Categorie[] newArray(int size) {
            return new Categorie[size];
        }
    };

    private Categorie(Parcel in) {
        libelle = in.readString();
    }
}
