import { Component, OnInit } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { NgbActiveModal } from '@ng-bootstrap/ng-bootstrap';
import { FormControl } from '@angular/forms';
import { Router } from '@angular/router';

interface UploadResponse {
  filePath: string;
}

interface Excursion {
  name: string;
  categories: string[];
  destination: string;
  banner: string;
}

@Component({
  selector: 'app-modal-form',
  templateUrl: './modal-form.component.html',
  styleUrls: ['./modal-form.component.scss']
})
export class ModalFormComponent implements OnInit {
  excursion: Excursion = {
    name: '',
    categories: [],
    destination: '',
    banner: ''
  };

  availableCategories: string[] = ['culture', 'sport', 'sahara', 'montagne', 'summer']; // Define your options here
  fileName = '';
  fileToUpload: File | null = null;
  iconAvailable = false;
  categoriesControl = new FormControl<string[]>(this.excursion.categories);

  constructor(public activeModal: NgbActiveModal, private http: HttpClient, private router: Router) {}

  ngOnInit(): void {
    this.iconAvailable = !!document.querySelector('mat-icon');
  }

  close() {
    this.activeModal.dismiss(); // Close the modal
  }

  onSubmit() {
    const selectedCategories = this.categoriesControl.value || [];
    this.excursion.categories = selectedCategories;
  
    const navigateToDetailsPage = () => {
      const queryParams = {
        name: this.excursion.name,
        destination: this.excursion.destination,
        banner: this.excursion.banner,
        categories: JSON.stringify(this.excursion.categories),
      };
  
      this.router.navigate(['/offers/offer-details'], { queryParams })
        .catch(err => console.error('Navigation error:', err));
    };
  
    if (this.fileToUpload) {
      const formData = new FormData();
      formData.append("thumbnail", this.fileToUpload);
      this.http.post<UploadResponse>("/api/thumbnail-upload", formData).subscribe({
        next: (response) => {
          this.excursion.banner = response.filePath; // Assuming the response contains the file path
          // Close the modal and navigate
          this.activeModal.close();
          navigateToDetailsPage();
        },
        error: (err) => {
          console.error('Upload failed', err);
        }
      });
    } else {
      // Close the modal and navigate if there's no file to upload
      this.activeModal.close();
      navigateToDetailsPage();
    }
  }
  

  navigateToDetailsPage() {
    const queryParams = {
      name: this.excursion.name,
      destination: this.excursion.destination,
      banner: this.excursion.banner,
      categories: JSON.stringify(this.excursion.categories),
    };

    this.router.navigate(['/offers/offer-details'], { queryParams })
      .catch(err => console.error('Navigation error:', err));
  }

  onFileSelected(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input && input.files && input.files.length) {
      const file = input.files[0];
      this.fileName = file.name;
      this.fileToUpload = file;
    }
  }
}
