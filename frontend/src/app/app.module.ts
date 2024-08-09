import { NgModule, APP_INITIALIZER } from '@angular/core';
import { BrowserModule } from '@angular/platform-browser';
import { BrowserAnimationsModule } from '@angular/platform-browser/animations';
import { provideHttpClient, withInterceptorsFromDi } from '@angular/common/http';
import { HttpClientInMemoryWebApiModule } from 'angular-in-memory-web-api';
import { ClipboardModule } from 'ngx-clipboard';
import { TranslateModule } from '@ngx-translate/core';
import { InlineSVGModule } from 'ng-inline-svg-2';
import { NgbModule } from '@ng-bootstrap/ng-bootstrap';
import { AppRoutingModule } from './app-routing.module';
import { AppComponent } from './app.component';
import { AuthService } from './modules/auth/services/auth.service';
import { FormsModule } from '@angular/forms'; // Import FormsModule
import { MatSelectModule } from '@angular/material/select';
import { MatFormFieldModule } from '@angular/material/form-field';
import { MatIconModule } from '@angular/material/icon';
import { ReactiveFormsModule } from '@angular/forms';
import { ModalFormComponent } from './modules/excursions/modal-form/modal-form.component';
import { MatButtonModule } from '@angular/material/button';
import { MatDialogModule } from '@angular/material/dialog';








import { HttpClientModule } from '@angular/common/http';
 // Import this

import { environment } from 'src/environments/environment';
// #fake-start#
import { FakeAPIService } from './_fake/fake-api.service';
// #fake-end#

function appInitializer(authService: AuthService) {
  return () => {
    return new Promise((resolve) => {
      // @ts-ignore
      authService.getUserByToken().subscribe().add(resolve);
    });
  };
}

@NgModule({ declarations: [AppComponent,],
    bootstrap: [AppComponent], imports: [BrowserModule,
        BrowserAnimationsModule,
        TranslateModule.forRoot(),
        ClipboardModule,
        HttpClientModule,
        FormsModule,
        MatFormFieldModule,
        MatSelectModule,
        MatIconModule,
        ReactiveFormsModule,
        MatButtonModule,
        MatDialogModule,

        // #fake-start#
        environment.isMockEnabled
            ? HttpClientInMemoryWebApiModule.forRoot(FakeAPIService, {
                passThruUnknownUrl: true,
                dataEncapsulation: false,
            })
            : [],
        // #fake-end#
        AppRoutingModule,
        InlineSVGModule.forRoot(),
        NgbModule], providers: [
        {
            provide: APP_INITIALIZER,
            useFactory: appInitializer,
            multi: true,
            deps: [AuthService],
        },
        provideHttpClient(withInterceptorsFromDi()),
    ] })
export class AppModule {}
