package com.example.matete;

import android.content.Context;
import android.view.LayoutInflater;
import android.view.View;
import android.view.ViewGroup;
import android.widget.ImageView;
import android.widget.TextView;

import androidx.recyclerview.widget.RecyclerView;

import com.example.matete.Model.Annonce;
import com.example.matete.Model.MarkerDataTag;
import com.squareup.picasso.Picasso;

import java.util.List;

public class FavorisAdapter extends RecyclerView.Adapter<FavorisAdapter.ViewHolder> {
    private List<MarkerDataTag> mData;
    private LayoutInflater mInflater;
    private ItemClickListener mClickListener;
    // data is passed into the constructor
    FavorisAdapter(Context context, List<MarkerDataTag> data) {
        this.mInflater = LayoutInflater.from(context);
        this.mData = data;
    }
    // inflates the row layout from xml when needed
    @Override
    public ViewHolder onCreateViewHolder(ViewGroup parent, int viewType) {
        View view = mInflater.inflate(R.layout.itemfavoris, parent, false);
        return new ViewHolder(view);
    }
    // binds the data to the TextView in each row
    @Override
    public void onBindViewHolder(ViewHolder holder, int position) {
        holder.nomAnnonce.setText(mData.get(position).getAnnonce().getNom());
        holder.descriptionAnnonce.setText(mData.get(position).getAnnonce().getDescription());
        Picasso.get().load(mData.get(position).getAnnonce().getImage()).resize(275, 183).into(holder.miniatureAnnonce);
    }
    // total number of rows
    @Override
    public int getItemCount() {
        if(mData == null){
            return 0;
        }else {
            return mData.size();
        }
    }
    // stores and recycles views as they are scrolled off screen
    public class ViewHolder extends RecyclerView.ViewHolder implements View.OnClickListener {
        TextView nomAnnonce;
        TextView descriptionAnnonce;
        ImageView miniatureAnnonce;
        ViewHolder(View itemView) {
            super(itemView);
            nomAnnonce = itemView.findViewById(R.id.nomAnnonce);
            descriptionAnnonce = itemView.findViewById(R.id.descriptionAnnonce);
            miniatureAnnonce = itemView.findViewById(R.id.miniatureAnnonce);
            itemView.setOnClickListener(this);
        }
        @Override
        public void onClick(View view) {
            if (mClickListener != null) mClickListener.onItemClick(view,
                    getAdapterPosition());
        }
    }
    // convenience method for getting data at click position
    String getItem(int id) {
        return mData.get(id).getAnnonce().getNom();
    }
    // allows clicks events to be caught
    void setClickListener(ItemClickListener itemClickListener) {
        this.mClickListener = itemClickListener;
    }
    // parent activity will implement this method to respond to click events
    public interface ItemClickListener {
        void onItemClick(View view, int position);
    }
}

