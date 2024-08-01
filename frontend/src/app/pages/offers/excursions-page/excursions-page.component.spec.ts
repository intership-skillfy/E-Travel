import { ComponentFixture, TestBed } from '@angular/core/testing';

import { ExcursionsPageComponent } from './excursions-page.component';

describe('ExcursionsPageComponent', () => {
  let component: ExcursionsPageComponent;
  let fixture: ComponentFixture<ExcursionsPageComponent>;

  beforeEach(() => {
    TestBed.configureTestingModule({
      declarations: [ExcursionsPageComponent]
    });
    fixture = TestBed.createComponent(ExcursionsPageComponent);
    component = fixture.componentInstance;
    fixture.detectChanges();
  });

  it('should create', () => {
    expect(component).toBeTruthy();
  });
});
