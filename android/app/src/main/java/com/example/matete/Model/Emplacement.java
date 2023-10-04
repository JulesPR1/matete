package com.example.matete.Model;

import android.content.Context;
import android.location.Geocoder;
import android.os.Parcel;
import android.os.Parcelable;

import androidx.test.core.app.ApplicationProvider;

import java.util.Locale;

public class Emplacement implements Parcelable{
    private String adresse;
    private String codePostale;

    public Emplacement(String adresse, String codePostale) {
        this.adresse = adresse;
        this.codePostale = codePostale;
    }

    public String getAdresse() {
        return adresse;
    }
    public String getCodePostale() {
        return codePostale;
    }

    ////pour les rendre parcelable
    public int describeContents() {
        return 0;
    }

    public void writeToParcel(Parcel out, int flags) {
        out.writeString(adresse);
        out.writeString(codePostale);
    }

    public static final Parcelable.Creator<Emplacement> CREATOR
            = new Parcelable.Creator<Emplacement>() {
        public Emplacement createFromParcel(Parcel in) {
            return new Emplacement(in);
        }

        public Emplacement[] newArray(int size) {
            return new Emplacement[size];
        }
    };

    private Emplacement(Parcel in) {
        adresse = in.readString();
        codePostale = in.readString();
    }
}
