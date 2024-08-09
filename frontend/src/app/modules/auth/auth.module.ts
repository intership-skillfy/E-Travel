import { NgModule } from '@angular/core';
import { CommonModule } from '@angular/common';
import { ReactiveFormsModule, FormsModule } from '@angular/forms';
import { provideHttpClient, withInterceptorsFromDi } from '@angular/common/http';
import { AuthRoutingModule } from './auth-routing.module';
import { LoginComponent } from './components/login/login.component';
import { ClientRegistrationComponent } from './components/clientRegistration/clientRegistration.component';
import { ForgotPasswordComponent } from './components/forgot-password/forgot-password.component';
import { LogoutComponent } from './components/logout/logout.component';
import { AuthComponent } from './auth.component';
import { TranslationModule } from '../i18n/translation.module';
import { AgencyRegisterComponent } from './components/agency-register/agency-register.component';
import { SignupComponent } from './components/signup/signup.component';

@NgModule({ declarations: [
        LoginComponent,
        ClientRegistrationComponent,
        ForgotPasswordComponent,
        LogoutComponent,
        AuthComponent,
        AgencyRegisterComponent,
        SignupComponent,
    ], imports: [CommonModule,
        TranslationModule,
        AuthRoutingModule,
        FormsModule,
        ReactiveFormsModule], providers: [provideHttpClient(withInterceptorsFromDi())] })
export class AuthModule {}
