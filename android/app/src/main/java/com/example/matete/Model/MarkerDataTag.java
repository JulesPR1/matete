package com.example.matete.Model;

/**
 * Cette classe sert a créer les tag des markers, afin de leur attribuer des infos supplémentaires (ici ville et catégorie
 */
public class MarkerDataTag {

    private String ville;
    private Annonce annonce;

    public MarkerDataTag(String ville, Annonce annonce) {
        this.ville = ville;
        this.annonce = annonce;
    }

    public String getVille() {
        return ville;
    }

    public Annonce getAnnonce() {
        return annonce;
    }
}
