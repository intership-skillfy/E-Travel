import { Component, OnDestroy, OnInit } from '@angular/core';
import { FormBuilder, FormGroup, Validators } from '@angular/forms';
import { Observable, Subscription } from 'rxjs';
import { AuthService } from '../../services/auth.service';
import { Router } from '@angular/router';
import { ConfirmPasswordValidator } from '../clientRegistration/confirm-password.validator';
import { first } from 'rxjs/operators';

@Component({
  selector: 'app-agency-register',
  templateUrl: './agency-register.component.html',
  styleUrls: ['./agency-register.component.scss'],
})
export class AgencyRegisterComponent implements OnInit, OnDestroy {
  registrationForm: FormGroup;
  hasError: boolean = false;
  isLoading$: Observable<boolean>;
  private unsubscribe: Subscription[] = [];
  logoToUpload: File | null = null;

  constructor(
    private fb: FormBuilder,
    private authService: AuthService,
    private router: Router
  ) {
    this.isLoading$ = this.authService.isLoading$;
    if (this.authService.currentUserValue) {
      this.router.navigate(['/']);
    }
  }

  ngOnInit(): void {
    this.initForm();
  }

  get f() {
    return this.registrationForm.controls;
  }

  initForm() {
    this.registrationForm = this.fb.group(
      {
        name: [
          '',
          [
            Validators.required,
            Validators.minLength(3),
            Validators.maxLength(100),
          ],
        ],
        email: [
          '',
          [
            Validators.required,
            Validators.email,
            Validators.minLength(3),
            Validators.maxLength(320),
          ],
        ],
        password: [
          '',
          [
            Validators.required,
            Validators.minLength(3),
            Validators.maxLength(100),
          ],
        ],
        cPassword: [
          '',
          [
            Validators.required,
            Validators.minLength(3),
            Validators.maxLength(100),
          ],
        ],
        phone: ['', Validators.required],
        addresse: ['', Validators.required],
        website: ['', Validators.required],
        logoUrl: [null], // Ensure to handle file input separately
        agree: [false, Validators.requiredTrue],
        role: 'ROLE_AGENCY',
      },
      {
        validator: ConfirmPasswordValidator.MatchPassword,
      }
    );
  }

  onLogoChange(event: Event) {
    const input = event.target as HTMLInputElement;
    if (input.files && input.files.length > 0) {
      this.logoToUpload = input.files[0];
    }
  }

  submit() {
    this.hasError = false;

    // Create a FormData object
    const formData = new FormData();

    // Add the JSON data
    const jsonData = {
      email: this.registrationForm.get('email')?.value,
      password: this.registrationForm.get('password')?.value,
      name: this.registrationForm.get('name')?.value,
      phone: this.registrationForm.get('phone')?.value,
      addresse: this.registrationForm.get('addresse')?.value,
      website: this.registrationForm.get('website')?.value,
      role: 'ROLE_AGENCY',
    };

    formData.append('json', JSON.stringify(jsonData));

    if (this.logoToUpload) {
      formData.append('logoUrl', this.logoToUpload, this.logoToUpload.name);
    }

    // Log the FormData for debugging
    formData.forEach((value, key) => {
      console.log(key, value);
    });

    const registrationSubscr = this.authService
      .registerClient(formData)
      .pipe(first())
      .subscribe(
        (response) => {
          console.log('Registration successful:', response);
          localStorage.setItem('token', response.token);
          localStorage.setItem('refreshToken', response.refreshToken);
          this.router.navigate(['/']);
        },
        (error) => {
          console.error('Registration error:', error);
          this.hasError = true;
        }
      );
    this.unsubscribe.push(registrationSubscr);
  }

  ngOnDestroy() {
    this.unsubscribe.forEach((sb) => sb.unsubscribe());
  }
}
