import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root'
})
export class OfferService {
  private apiUrl = `${environment.apiUrl}/api/offres`; 

  constructor(private http: HttpClient) {}

  getOffersByType(type: string): Observable<any> {
    const params = new HttpParams().set('type', type);
    return this.http.get(this.apiUrl, { params });
  }

  deleteOffer(id: number): Observable<void> {
    return this.http.delete<void>(`${this.apiUrl}/${id}/delete`);
  }

  getOfferDetails(id: number): Observable<any> {
    return this.http.get<any>(`${this.apiUrl}/${id}`);
  }

  updateOffer(id: number, offerData: any): Observable<any> {
    const url = `${this.apiUrl}/${id}/edit`;
    const headers = new HttpHeaders({ 'Content-Type': 'application/json' });
    return this.http.put(url, offerData, { headers });
  }
}

export interface Offer {
  banner: string;
  name: string;
  destination: string;
  categories: string[];
  type: string;
}
