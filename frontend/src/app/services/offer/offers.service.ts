import { Injectable } from '@angular/core';
import { HttpClient, HttpHeaders, HttpParams } from '@angular/common/http';
import { Observable } from 'rxjs';
import { environment } from 'src/environments/environment';

@Injectable({
  providedIn: 'root',
})
export class OffersService {
  private apiUrl = `${environment.apiUrl}/api/offres`;

  constructor(private http: HttpClient) {}

  createOffer(offerData: any): Observable<any> {
    return this.http.post(`${this.apiUrl}/new`, offerData);
  }

  getOffersByType(type: string): Observable<any> {
    const params = new HttpParams().set('type', type);
    console.log('Fetching offers with type:', type); // Debugging log
    return this.http.get(this.apiUrl, { params });
  }

  deleteExcursion(id: number): Observable<void> {
    console.log('Deleting offer with ID:', id); // Debugging log
    return this.http.delete<void>(`${this.apiUrl}/${id}/delete`);
  }

  getOfferById(id: number): Observable<any> {
    console.log('Fetching offer with ID:', id); // Debugging log
    return this.http.get<any>(`${this.apiUrl}/${id}`);
  }

  updateOffer(id: number, offerData: any): Observable<any> {
    const url = `${this.apiUrl}/${id}/edit`;
    const headers = new HttpHeaders({ 'Content-Type': 'application/json' });
    console.log('Updating offer with ID:', id, 'and data:', offerData); // Debugging log
    return this.http.put(url, offerData, { headers });
  }
}
