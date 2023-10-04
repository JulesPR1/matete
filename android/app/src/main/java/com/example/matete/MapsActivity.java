package com.example.matete;

import androidx.annotation.NonNull;
import androidx.core.app.ActivityCompat;
import androidx.fragment.app.FragmentActivity;

import android.Manifest;
import android.annotation.SuppressLint;
import android.content.Context;
import android.content.Intent;
import android.content.pm.PackageManager;
import android.location.Geocoder;
import android.location.Location;
import android.location.LocationManager;
import android.nfc.Tag;
import android.os.Bundle;
import android.os.Handler;
import android.os.Looper;
import android.provider.Settings;
import android.text.Editable;
import android.text.TextWatcher;
import android.util.Log;
import android.view.View;
import android.widget.AdapterView;
import android.widget.ArrayAdapter;
import android.widget.AutoCompleteTextView;
import android.widget.Button;
import android.widget.EditText;
import android.widget.ImageButton;
import android.widget.ImageView;
import android.widget.Spinner;
import android.widget.TextView;
import android.widget.Toast;

import com.android.volley.Request;
import com.android.volley.Response;
import com.android.volley.VolleyError;
import com.android.volley.toolbox.JsonObjectRequest;
import com.example.matete.Model.Annonce;
import com.example.matete.Model.Categorie;
import com.example.matete.Model.Emplacement;
import com.example.matete.Model.MarkerDataTag;
import com.example.matete.Model.Vendeur;
import com.google.android.gms.location.FusedLocationProviderClient;
import com.google.android.gms.location.LocationCallback;
import com.google.android.gms.location.LocationRequest;
import com.google.android.gms.location.LocationResult;
import com.google.android.gms.location.LocationServices;
import com.google.android.gms.maps.CameraUpdateFactory;
import com.google.android.gms.maps.GoogleMap;
import com.google.android.gms.maps.OnMapReadyCallback;
import com.google.android.gms.maps.SupportMapFragment;
import com.google.android.gms.maps.model.BitmapDescriptorFactory;
import com.google.android.gms.maps.model.LatLng;
import com.google.android.gms.maps.model.Marker;
import com.google.android.gms.maps.model.MarkerOptions;
import com.example.matete.databinding.ActivityMapsBinding;

import org.json.JSONArray;
import org.json.JSONObject;

import com.example.librairie2.Model.MySingleton;
import com.google.android.gms.tasks.OnCompleteListener;
import com.google.android.gms.tasks.Task;
import com.google.android.material.floatingactionbutton.FloatingActionButton;
import com.squareup.picasso.Picasso;

import java.util.ArrayList;
import java.util.Locale;
import java.util.Timer;
import java.util.TimerTask;
import java.util.concurrent.TimeUnit;

public class MapsActivity extends FragmentActivity implements OnMapReadyCallback, GoogleMap.OnInfoWindowClickListener {

    private GoogleMap mMap;
    private ActivityMapsBinding binding;

    private ArrayList<Annonce> toutesLesAnnonces = new ArrayList();
    private ArrayList<Categorie> toutesLesCategories = new ArrayList();
    private ArrayList<Marker> lesMarkers = new ArrayList();
    private ArrayList<Marker> annoncesFavorites = new ArrayList();

    private ArrayAdapter<Categorie> adapter =null;
    private ArrayAdapter<String> autocompleteAdapter;

    private EditText rechercheInput;
    private Spinner spinnerCategorie;
    private Spinner spinnerDistance;

    private FusedLocationProviderClient mFusedLocationClient;

    private LatLng user;

    //annonce locale pour test
    private Annonce annonceLocal;
    private Vendeur vendeurLocal;
    private Categorie categorieLocal;
    private Emplacement emplacementLocal;

    private FloatingActionButton btnFavoris;
    private FloatingActionButton btnRefresh;

    private String urlAnnonces = "http://192.168.1.25/API/?action=all";
    private String urlCatégories = "http://192.168.1.25/API/?action=cat";


    @Override
    protected void onCreate(Bundle savedInstanceState) {
        super.onCreate(savedInstanceState);

        mFusedLocationClient = LocationServices.getFusedLocationProviderClient(this);
        // methode pour récup la localisation de l'utilisateur
        getLastLocation();
        // récupère les données accessibles via l'API
        recupererLesAnnoncesHTTP();
        recupererLesCategoriesHTTP();

        rechercheInput = findViewById(R.id.recherche);
        spinnerCategorie = (Spinner)findViewById(R.id.categorie);
        //Actualise les données toutes les 20s si la barre de recherche est vide
        final Handler handler = new Handler();
        handler.postDelayed(new Runnable() {
            public void run() {
                if(rechercheInput.getText().toString().isEmpty() && spinnerCategorie.getSelectedItem().toString().equals("Tout") && spinnerDistance.getSelectedItem().toString().equals("Tout")) {
                    recupererLesAnnoncesHTTP();
                    recupererLesCategoriesHTTP();
                    Log.e("", "Nouveau points actualisés");
                }else{
                    Log.e("", "non actualisée car recherche en cours: " + rechercheInput.getText().toString());
                }
                handler.postDelayed(this, 10000);
            }
        }, 10000);//se relance toute les 20s

        //supprime les points obsolètes toutes les 60s (fait clignoter les points)

        /*
        final Handler refreshMap = new Handler();
        refreshMap.postDelayed(new Runnable() {
            public void run() {
                if(rechercheInput.getText().toString().isEmpty() && spinnerCategorie.getSelectedItem().toString().equals("Tout") && spinnerDistance.getSelectedItem().toString().equals("Tout")) {
                    delPointsObsoletes();
                }
                refreshMap.postDelayed(this, 11000);
                Log.e("", "Vieux points actualisés");
            }
        }, 9000);//se relance toute les 10min
        */

        binding = ActivityMapsBinding.inflate(getLayoutInflater());
        setContentView(binding.getRoot());

        // Obtain the SupportMapFragment and get notified when the map is ready to be used.
        SupportMapFragment mapFragment = (SupportMapFragment) getSupportFragmentManager()
                .findFragmentById(R.id.map);
        mapFragment.getMapAsync(this);

        ///spinner catégorie
        adapter = new ArrayAdapter<Categorie>(getApplicationContext(), R.layout.custom_drop_down_spinner_item, toutesLesCategories);
        adapter.setDropDownViewResource(R.layout.support_simple_spinner_dropdown_item);
        spinnerCategorie = (Spinner)findViewById(R.id.categorie);
        spinnerCategorie.setAdapter(adapter);
        spinnerCategorie.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parentView, View selectedItemView, int position, long id) {
                trieCategorie();
                spinnerDistance.setSelection(0);
            }

            @Override
            public void onNothingSelected(AdapterView<?> parentView) {
                // your code here
            }
        });

        //spinner distance
        spinnerDistance = findViewById(R.id.distance);
        spinnerDistance.setOnItemSelectedListener(new AdapterView.OnItemSelectedListener() {
            @Override
            public void onItemSelected(AdapterView<?> parentView, View selectedItemView, int position, long id) {
                trieDistance();
            }

            @Override
            public void onNothingSelected(AdapterView<?> parentView) {
                // your code here
            }
        });

        //barre de recherche
        rechercheInput = findViewById(R.id.recherche);
        rechercheInput.addTextChangedListener(new TextWatcher() {

            @Override
            public void onTextChanged(CharSequence s, int start, int before, int count) {
                String recherche = rechercheInput.getText().toString();

                //remet en visible seulement ceux recherchés
                for (int i=0; i<lesMarkers.size(); i++){
                    Marker currentMarker = lesMarkers.get(i);
                    //récupère la data supplémentaire du marker
                    MarkerDataTag currentMarkerDATA = (MarkerDataTag)(currentMarker.getTag());

                    if(currentMarker.getTitle().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))
                            || currentMarker.getSnippet().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))
                            || currentMarkerDATA.getAnnonce().getCategorie().getLibelle().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))
                            || currentMarkerDATA.getVille().toLowerCase(Locale.ROOT).contains(recherche.toLowerCase(Locale.ROOT))){
                        currentMarker.setVisible(true);
                    }else{
                        currentMarker.setVisible(false);
                    }
                }
            }

            @Override
            public void beforeTextChanged(CharSequence s, int start, int count, int after) {

            }

            @Override
            public void afterTextChanged(Editable s) {

            }
        });

        //boutons pour la page favoris
        btnFavoris = findViewById(R.id.favoris);
        btnFavoris.setOnClickListener(new View.OnClickListener()
        {
            public void onClick(View v) {
                Intent intent = new Intent(MapsActivity.this, FavorisActivity.class);
                intent.putExtra("nbAnnoncesFav", annoncesFavorites.size());
                for(int i = 0; i < annoncesFavorites.size(); i++){
                    Marker currentAnnonce = annoncesFavorites.get(i);
                    MarkerDataTag markerDATA = ((MarkerDataTag) currentAnnonce.getTag());

                    intent.putExtra("Annonce" + i, markerDATA.getAnnonce());
                    intent.putExtra("Vendeur" + i, markerDATA.getAnnonce().getVendeur());
                    intent.putExtra("Categorie" + i, markerDATA.getAnnonce().getCategorie());
                    intent.putExtra("Emplacement" + i, markerDATA.getAnnonce().getEmplacement());
                    intent.putExtra("Ville" + i, markerDATA.getVille());
                }
                startActivity(intent);
            }
        });

        btnRefresh = findViewById(R.id.refresh);
        btnRefresh.setOnClickListener(new View.OnClickListener()
        {
            public void onClick(View v) {
               delPointsObsolètes();
                final Handler refreshMap = new Handler();
                refreshMap.postDelayed(new Runnable() {
                    public void run() {
                            recupererLesAnnoncesHTTP();
                            recupererLesCategoriesHTTP();
                            ;
                    }
                }, 1000);
            }
        });
    }

    private void trieDistance(){
         if (user != null) {
                if (!spinnerDistance.getSelectedItem().toString().replaceAll("[^0-9]", "").isEmpty()) {
                    //prend la distance souhaitée en km
                    int distanceMax = Integer.parseInt(spinnerDistance.getSelectedItem().toString().replaceAll("[^0-9]", ""));

                    //convertie la distance en mètres
                    distanceMax = distanceMax * 1000;

                    //creer la Localisation de l'utilisateur
                    Location userLocalisation = new Location("user");
                    userLocalisation.setLatitude(user.latitude);
                    userLocalisation.setLongitude(user.longitude);

                    for (int i = 0; i < lesMarkers.size(); i++) {
                        Marker currentMarker = lesMarkers.get(i);

                        //creer la Localisation de l'annonce
                        Location annonceLocalisation = new Location("Annonce");
                        annonceLocalisation.setLatitude(currentMarker.getPosition().latitude);
                        annonceLocalisation.setLongitude(currentMarker.getPosition().longitude);

                        //trouve la distance entre la Localisation de l'utilisateur et la Localisation de l'annonce
                        double distance = userLocalisation.distanceTo(annonceLocalisation);
                        Log.e("Distance: ", currentMarker.getTitle() + " " + String.valueOf(distance) + " " + String.valueOf(distanceMax));

                        //si la distance calculée est inferieure à la distanceMax choisie sur le spinner :
                        if (distance < distanceMax) {
                            currentMarker.setVisible(true);
                        } else {
                            currentMarker.setVisible(false);
                        }
                    }
                } else {
                    for (int i = 0; i < lesMarkers.size(); i++) {
                        lesMarkers.get(i).setVisible(true);
                    }
                }
         }
    }


    private void trieCategorie(){
            String selectedCategorie = spinnerCategorie.getSelectedItem().toString();
            Log.e("", "Catégorie : " + selectedCategorie);

            if (!selectedCategorie.equals("Tout")) {
                //met en invisible les marker n'ayant pas la catégorie souhaitée
                for (int i = 0; i < lesMarkers.size(); i++) {
                    //on récupère la data catégorie dans le tag du marker
                    String LBLcategorieMarker = ((MarkerDataTag) lesMarkers.get(i).getTag()).getAnnonce().getCategorie().getLibelle();
                    Log.e("", lesMarkers.get(i).getTitle() + ": " + LBLcategorieMarker);
                    if (LBLcategorieMarker.equals(selectedCategorie)) {
                        lesMarkers.get(i).setVisible(true);
                    } else {
                        lesMarkers.get(i).setVisible(false);
                    }
                }
            } else {
                for (int i = 0; i < lesMarkers.size(); i++) {
                    lesMarkers.get(i).setVisible(true);
                }
            }
    }

    @SuppressLint("MissingPermission")
    private void getLastLocation(){
        // regarde si les permissions sont ok
        if (checkPermissions()) {
            // regarde si la localisation est activée
            if (isLocationEnabled()) {
                mFusedLocationClient.getLastLocation().addOnCompleteListener(new OnCompleteListener<Location>() {
                    @Override
                    public void onComplete(@NonNull Task<Location> task) {
                        Location location = task.getResult();
                        if (location == null) {
                            Log.e("", "location est null");
                            requestNewLocationData();
                        }else {
                            Log.e("", "localisation user: " + String.valueOf(location.getLatitude()) + " " + String.valueOf(location.getLongitude()));
                            if (location.getLatitude() != 0 || location.getLongitude() != 0) {
                                //ajoute le point de l'utilisateur sur la map
                                user = new LatLng(location.getLatitude(), location.getLongitude());
                                mMap.addMarker(new MarkerOptions().position(user).title("Ma position"));
                                mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(user, 10));
                            }
                        }
                    }
                });
            } else {
                //demande d'activer la localisation si elle ne l'est pas
                Toast.makeText(this, "Merci d'activer" + " votre localisation...", Toast.LENGTH_LONG).show();
                //renvoie directement dans la bonne page des paramètres android
                Intent intent = new Intent(Settings.ACTION_LOCATION_SOURCE_SETTINGS);
                startActivity(intent);
            }
        } else {
            //demande les permissions si elles ne sont pas dispo
            requestPermissions();
        }
    }
    @SuppressLint("MissingPermission")
    private void requestNewLocationData() {

        // requete localisation
        LocationRequest mLocationRequest = new LocationRequest();
        mLocationRequest.setPriority(LocationRequest.PRIORITY_HIGH_ACCURACY);
        mLocationRequest.setInterval(5);
        mLocationRequest.setFastestInterval(0);
        mLocationRequest.setNumUpdates(1);

        mFusedLocationClient = LocationServices.getFusedLocationProviderClient(this);
        mFusedLocationClient.requestLocationUpdates(mLocationRequest, mLocationCallback, Looper.myLooper());
    }

    private LocationCallback mLocationCallback = new LocationCallback() {

        @Override
        public void onLocationResult(LocationResult locationResult) {
            Location mLastLocation = locationResult.getLastLocation();

            if (mLastLocation.getLatitude() != 0 || mLastLocation.getLongitude() != 0) {
                //ajoute le point de l'utilisateur sur la map
                user = new LatLng(mLastLocation.getLatitude(), mLastLocation.getLongitude());
                mMap.addMarker(new MarkerOptions().position(user).title("Ma position"));
                mMap.moveCamera(CameraUpdateFactory.newLatLngZoom(user, 10));
            }
        }
    };

    // check les permissions de localisation
    private boolean checkPermissions() {
        return ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_COARSE_LOCATION) == PackageManager.PERMISSION_GRANTED && ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_FINE_LOCATION) == PackageManager.PERMISSION_GRANTED;
        // ActivityCompat.checkSelfPermission(this, Manifest.permission.ACCESS_BACKGROUND_LOCATION) == PackageManager.PERMISSION_GRANTED
    }

    // demande les permissions
    private void requestPermissions() {
        ActivityCompat.requestPermissions(this, new String[]{
                Manifest.permission.ACCESS_COARSE_LOCATION,
                Manifest.permission.ACCESS_FINE_LOCATION}, 44);
    }


    // check si la geolocalisation de l'appareil est activée
    private boolean isLocationEnabled() {
        LocationManager locationManager = (LocationManager) getSystemService(Context.LOCATION_SERVICE);
        return locationManager.isProviderEnabled(LocationManager.GPS_PROVIDER) || locationManager.isProviderEnabled(LocationManager.NETWORK_PROVIDER);
    }

    // si tout les paramètres de localisation son OK alors
    @Override
    public void
    onRequestPermissionsResult(int requestCode, @NonNull String[] permissions, @NonNull int[] grantResults) {
        super.onRequestPermissionsResult(requestCode, permissions, grantResults);

        if (requestCode == 44) {
            if (grantResults.length > 0 && grantResults[0] == PackageManager.PERMISSION_GRANTED) {
                getLastLocation();
            }
        }
    }

    @Override
    public void onResume() {
        super.onResume();
        if (checkPermissions()) {
            getLastLocation();
        }
    }

    //récupère les annonces sur l'API
    public void recupererLesAnnoncesHTTP(){
        toutesLesAnnonces.clear();

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.GET, urlAnnonces, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                try {
                    JSONArray annonces = response.getJSONArray("annonces");

                    for (int i = 0; i < annonces.length(); i++) {
                        JSONObject annonce = annonces.getJSONObject(i);
                        //Get the current json object data
                        String id = annonce.getString("id");
                        String nom = annonce.getString("nom");
                        String description = annonce.getString("description");
                        String quantite = annonce.getString("quantite");
                        String image = annonce.getString("image");

                        Vendeur vendeur = new Vendeur(annonce.getJSONObject("vendeur").getString("id"), annonce.getJSONObject("vendeur").getString("nom"), annonce.getJSONObject("vendeur").getString("numero"), annonce.getJSONObject("vendeur").getString("mail"));
                        Categorie categorie = new Categorie(annonce.getJSONObject("categorie").getString("libelle"));
                        Emplacement emplacement = new Emplacement(annonce.getJSONObject("emplacement").getString("adresse"), annonce.getJSONObject("emplacement").getString("codePostale"));

                        Annonce annonceObjet = new Annonce(id, nom, description, quantite, image, vendeur, categorie, emplacement);

                        Log.e(nom, id);

                        toutesLesAnnonces.add(annonceObjet);
                    }
                    //on traduit les adresses en positions
                    recupPositionsHTTP();
                } catch (Exception e) {
                    Log.e("erreur recup annonce", e.getMessage());
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e("",error.toString());
            }
        });

        // Access the RequestQueue through your singleton class.
        MySingleton.getInstance(this).addToRequestQueue(jsonObjectRequest);
    }

    //récupère les positions grace a l'api du gouv et place les points des annonces sur la map
    public void recupPositionsHTTP(){
        for(int i = 0; i < toutesLesAnnonces.size(); i++){
            Annonce annonce = toutesLesAnnonces.get(i);
            //récuperation de la geolocalisation de l'adresse
            String url = "https://api-adresse.data.gouv.fr/search/?q=" + toutesLesAnnonces.get(i).getEmplacement().getAdresse().replaceAll(" ", "+").toLowerCase()  + "&postcode=" +toutesLesAnnonces.get(i).getEmplacement().getCodePostale();
            //Log.e("url", url);
            JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.GET, url, null, new Response.Listener<JSONObject>() {
                @Override
                public void onResponse(JSONObject response) {
                    try {
                        JSONArray position = response.getJSONArray("features").getJSONObject(0).getJSONObject("geometry").getJSONArray("coordinates");
                        String Longitude = position.getString(0);
                        String Latitude = position.getString(1);
                        //localisation du point
                        LatLng point = new LatLng(Double.parseDouble(Latitude), Double.parseDouble(Longitude));

                        //récuperation de la ville (utile pour les recherches, pas dans l'API php donc on la prend avec celle ci)
                        //on enverra ensuite la ville dans la DATA supplémentaire du marker
                        String ville = response.getJSONArray("features").getJSONObject(0).getJSONObject("properties").getString("city");

                        //Création et pose le point sur la map
                        poseDePoint(annonce, point, ville);

                    } catch (Exception e) {
                        Log.e("erreur recup positions", e.getMessage() + e.getLocalizedMessage());
                    }
                }
            }, new Response.ErrorListener() {
                @Override
                public void onErrorResponse(VolleyError error) {
                    Log.e("",error.toString());
                }
            });
            // Access the RequestQueue through your singleton class.
            MySingleton.getInstance(this).addToRequestQueue(jsonObjectRequest);
        }
    }

    //pose le point de l'annonce sur la map
    public void poseDePoint(Annonce annonce, LatLng point, String ville){
        Boolean existeDeja = false;

        //met la catégorie et la ville de l'annonce en tag (via l'objet MarkerDataTag) du point pour pouvoir y acceder
        MarkerDataTag markerData = new MarkerDataTag(ville, annonce);

        if(!lesMarkers.isEmpty()){
            //check si l'annonce existe deja grace au modeleMarker
            for(int i = 0; i<lesMarkers.size(); i++){
                MarkerDataTag CurrentMarkerDATA = ((MarkerDataTag) lesMarkers.get(i).getTag());
                if (annonce.getId().equals(CurrentMarkerDATA.getAnnonce().getId())){
                    existeDeja = true;
                }
            }
        }else{
            Marker annonceMarker = mMap.addMarker(new MarkerOptions().position(point).title(annonce.getNom()).snippet(annonce.getDescription()).icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_GREEN)));
            annonceMarker.setTag(markerData);
            lesMarkers.add(annonceMarker);
        }

        //pose le point si il n'existe pas
        if(!existeDeja){
            Marker annonceMarker = mMap.addMarker(new MarkerOptions().position(point).title(annonce.getNom()).snippet(annonce.getDescription()).icon(BitmapDescriptorFactory.defaultMarker(BitmapDescriptorFactory.HUE_GREEN)));
            annonceMarker.setTag(markerData);
            mMap.setOnInfoWindowClickListener(this::onInfoWindowClick);
            lesMarkers.add(annonceMarker);
        }
    }

    public void delPointsObsolètes(){
        //clear les points afin de retirer les points obsolètes
        for(int i = 0; i<lesMarkers.size(); i++){
            Marker currentMarker = lesMarkers.get(i);
            currentMarker.remove();
        }
        lesMarkers.clear();
    }

    //récupère les catégories sur l'API
    public void recupererLesCategoriesHTTP(){
        toutesLesCategories.clear();

        toutesLesCategories.add(new Categorie("Tout"));

        JsonObjectRequest jsonObjectRequest = new JsonObjectRequest(Request.Method.GET, urlCatégories, null, new Response.Listener<JSONObject>() {
            @Override
            public void onResponse(JSONObject response) {
                try {
                    JSONArray categories = response.getJSONArray("categories");

                    for (int i = 0; i < categories.length(); i++) {
                        JSONObject categorie = categories.getJSONObject(i);
                        //Get the current json object data
                        String libelle = categorie.getString("libelle");

                        toutesLesCategories.add(new Categorie(libelle));
                    }
                    //prévenir le spinner de l'actualisation des données
                    adapter.notifyDataSetChanged();

                } catch (Exception e) {
                    Log.e("erreur", e.getMessage());
                }
            }
        }, new Response.ErrorListener() {
            @Override
            public void onErrorResponse(VolleyError error) {
                Log.e("",error.toString());
            }
        });

        // Access the RequestQueue through your singleton class.
        MySingleton.getInstance(this).addToRequestQueue(jsonObjectRequest);
    }

    @Override
    public void onMapReady(GoogleMap googleMap){
        mMap = googleMap;
        mMap.setInfoWindowAdapter(new GoogleMap.InfoWindowAdapter() {
            @Override
            public View getInfoWindow(Marker marker) {
                return null;
            }

            @Override
            public View getInfoContents(Marker marker) {
                if (!marker.getTitle().equals("Ma position")) {
                    MarkerDataTag markerData = ((MarkerDataTag) marker.getTag());
                    View infoWindow = getLayoutInflater().inflate(R.layout.fenetre_personnalisee_marker, null);
                    // Setting up the infoWindow with current's marker info
                    TextView libelle = infoWindow.findViewById(R.id.libelle);
                    libelle.setText(marker.getTitle());
                    //prend l'image avec l'url, la resize et la place
                    ImageView image = infoWindow.findViewById(R.id.imageFenetre);
                    Picasso.get().load(markerData.getAnnonce().getImage()).resize(275, 183).into(image);
                    return infoWindow;
                }
                return null;
            }
        });
    }

    @Override
    public void onInfoWindowClick(@NonNull Marker marker) {
        if(!marker.getTitle().equals("Ma position")) {
            Intent intent = new Intent(MapsActivity.this, DetailActivity.class);

            MarkerDataTag markerDATA = ((MarkerDataTag) (marker.getTag()));
            intent.putExtra("Annonce", markerDATA.getAnnonce());
            intent.putExtra("Vendeur", markerDATA.getAnnonce().getVendeur());
            intent.putExtra("Categorie", markerDATA.getAnnonce().getCategorie());
            intent.putExtra("Emplacement", markerDATA.getAnnonce().getEmplacement());
            intent.putExtra("Ville", markerDATA.getVille());
            if(annoncesFavorites != null){
                for (int i = 0; i < annoncesFavorites.size(); i++) {
                    MarkerDataTag currentFavDATA = ((MarkerDataTag)(annoncesFavorites.get(i).getTag()));
                    if(markerDATA.getAnnonce().getId().equals(currentFavDATA.getAnnonce().getId())){
                        intent.putExtra("isFavorite", true);
                    }
                }
            }
            startActivityForResult(intent, 1);
        }
    }

    protected void onActivityResult(int requestCode, int resultCode, Intent data)
    {
        super.onActivityResult(requestCode, resultCode, data);
        // check if the request code is same as what is passed  here it is 1
        if(requestCode==1)
        {
            String idAnnonceFavoris = data.getStringExtra("idAnnonceFavoris");
            Boolean canceled = data.getBooleanExtra("canceled?", false);
            for (int i = 0; i<lesMarkers.size(); i++){
                Marker currentAnnonce = lesMarkers.get(i);
                MarkerDataTag markerDATA = ((MarkerDataTag) currentAnnonce.getTag());
                if(markerDATA.getAnnonce().getId().equals(idAnnonceFavoris)){
                    Marker recherché = currentAnnonce;
                    if(canceled)
                    {
                        annoncesFavorites.remove(recherché);
                    }else{
                        annoncesFavorites.add(recherché);
                    }
                }
            }
            Log.e("id annonce favoris ", idAnnonceFavoris);
            Log.e("les annonces favorites", annoncesFavorites.toString());
        }
    }

    @Override
    public void onPointerCaptureChanged(boolean hasCapture) {

    }
}