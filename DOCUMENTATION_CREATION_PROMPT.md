# Prompt: Create Software Developer Offboarding Documentation

Use this prompt with an AI assistant (like Cursor AI, ChatGPT, Claude, etc.) to generate comprehensive offboarding documentation for any software project.

---

## Main Prompt

```
I need you to create comprehensive Software Developer Offboarding Documentation for my [PROJECT_NAME] project. 

Please analyze my codebase and create documentation following this structure:

## Required Sections:

### 1. Executive Summary (For Non-Technical Stakeholders)
- What the system does in plain language
- Who uses it (user types/roles)
- Key business processes explained simply
- Main features listed
- Important business rules
- System status

### 2. Getting Started Guide (For New Developers)
- Welcome message
- Prerequisites checklist
- First day checklist
- Step-by-step setup instructions with explanations
- Learning path (first week)
- Common first-time issues and solutions
- Framework/language basics explained
- Where to get help

### 3. Project & System
- High-Level Architecture
  - System overview
  - Technology stack (with explanations of why each is used)
  - System architecture diagram (use ASCII art for PDF compatibility)
  - Core modules
- Project Setup Guide
  - Prerequisites
  - Installation steps
  - Environment configuration (.env examples)
- API Documentation
  - Authentication
  - Key endpoints
  - Error codes
- Coding Standards
  - Naming conventions
  - Folder structure
  - Linting rules

### 4. Code-Level
- Work-in-Progress Report
  - Completed features ✅
  - In-Progress tasks 🔄
  - Pending features 📋
- Critical Code Areas
  - Payment processing (if applicable)
  - Order lifecycle (if applicable)
  - Scheduled tasks/cron jobs
  - Key integrations
  - Important business logic
- Known Bugs & Limitations
  - Known issues with workarounds
  - Limitations
  - Temporary workarounds

### 5. Troubleshooting Guide
- Common issues organized by category
- Solutions for each issue
- Debugging tips
- How to get help

### 6. DevOps & Infrastructure
- Deployment Process
  - Step-by-step deployment
  - CI/CD flow (if applicable)
  - Rollback steps
  - Common deployment issues
- Server/Cloud Documentation
  - Server information
  - Access locations
  - Monitoring
  - Backups
- Environment Configuration
  - Differences between DEV/STAGING/PROD
  - Configuration notes

### 7. Database
- Database Schema (ERD)
  - Key tables
  - Relationships
  - Indexes
- Migration History
  - Location of migrations
  - Manual DB steps (if any)

### 8. Operations
- Common Tasks (Step-by-Step Guides)
  - For administrators
  - For developers
- User/Admin Manual
  - How to use system dashboards
  - Admin console usage
- Maintenance Tasks
  - Cron jobs
  - Log rotation
  - SSL renewal
  - Data cleanup schedule

### 9. Access & Accounts
- Credential Inventory
  - Application credentials
  - Server access
  - Third-party services
  - Admin accounts
- Security notes

### 10. Glossary of Terms
- Technical terms explained simply
- Business terms explained
- Acronyms defined
- Common phrases

### 11. Master Handover Document
- Project summary
- Quick links to all documentation
- Project status
- Critical information
- Handover checklist

## Requirements:

1. **For Non-IT Persons:**
   - Use plain language
   - Explain technical terms
   - Focus on business processes
   - Add "what does this mean?" explanations

2. **For New Developers:**
   - Add context and explanations
   - Explain "why" not just "what" and "how"
   - Include learning paths
   - Add troubleshooting help

3. **Technical Details:**
   - Be comprehensive
   - Include code examples
   - Reference actual file paths
   - Document all integrations

4. **Format:**
   - Use Markdown format
   - Use ASCII art for diagrams (not Unicode box-drawing characters)
   - Include code blocks with syntax highlighting
   - Use tables where appropriate
   - Add checklists for tasks

5. **Analysis:**
   - Analyze the codebase structure
   - Identify all key components
   - Document all routes/endpoints
   - List all models and relationships
   - Document all services/integrations
   - Identify scheduled tasks
   - List all configuration files

Please create this documentation by:
1. First exploring the codebase to understand the project
2. Then creating the documentation following the structure above
3. Making it accessible for both technical and non-technical readers
```

---

## Alternative: Step-by-Step Approach

If you prefer to do it step by step, use these prompts in sequence:

### Step 1: Analysis
```
Analyze my [PROJECT_NAME] codebase and provide:
- Technology stack
- Main features/modules
- Database structure
- Key integrations
- Scheduled tasks
- Deployment method
```

### Step 2: Executive Summary
```
Based on the analysis, create an Executive Summary section that explains:
- What the system does in plain language
- Who uses it
- Key business processes
- Main features
- System status
```

### Step 3: Getting Started Guide
```
Create a Getting Started Guide for new developers including:
- Prerequisites checklist
- Step-by-step setup
- First day checklist
- Learning path
- Common issues
```

### Step 4: Technical Documentation
```
Create technical documentation covering:
- Architecture overview
- Setup guide
- API documentation
- Critical code areas
- Database schema
- Deployment process
```

### Step 5: Operations & Maintenance
```
Create operations documentation including:
- Common tasks
- Troubleshooting guide
- Maintenance tasks
- Access & accounts
```

### Step 6: Final Touches
```
Add:
- Glossary of terms
- Master handover document
- Any missing sections
```

---

## Customization Tips

### For Different Project Types

**Web Application:**
- Focus on routes, controllers, views
- Document API endpoints
- Include frontend framework details

**API Project:**
- Emphasize API documentation
- Document authentication methods
- Include request/response examples

**Mobile App:**
- Document mobile-specific setup
- Include build/deployment for iOS/Android
- Document API integration

**Microservices:**
- Document each service separately
- Include service communication
- Document deployment orchestration

### For Different Frameworks

**Laravel (PHP):**
- Document migrations, models, controllers
- Include Artisan commands
- Document service providers

**React/Next.js:**
- Document component structure
- Include build process
- Document state management

**Node.js/Express:**
- Document npm scripts
- Include middleware documentation
- Document API routes

**Python/Django:**
- Document models, views, URLs
- Include management commands
- Document settings

---

## Quick Start Checklist

When creating documentation for a new project:

- [ ] Analyze codebase structure
- [ ] Identify technology stack
- [ ] List all features/modules
- [ ] Document database schema
- [ ] List all integrations
- [ ] Document deployment process
- [ ] Create Executive Summary
- [ ] Create Getting Started Guide
- [ ] Document API/endpoints
- [ ] List known issues
- [ ] Create troubleshooting guide
- [ ] Document common tasks
- [ ] Create glossary
- [ ] Generate PDF (if needed)

---

## PDF Generation (Optional)

If you want to generate a PDF like we did for MyGooners:

1. **Create Artisan Command:**
   ```bash
   php artisan make:command GenerateDocumentationPDF
   ```

2. **Copy the PDF generator code** from `app/Console/Commands/GenerateDocumentationPDF.php`

3. **Customize** the markdown-to-HTML conversion for your needs

4. **Generate PDF:**
   ```bash
   php artisan docs:generate-pdf
   ```

---

## Example Prompts for Specific Sections

### For API Documentation:
```
Document all API endpoints in my project including:
- HTTP method and URL
- Authentication requirements
- Request parameters
- Response format
- Error codes
- Example requests/responses
```

### For Database Schema:
```
Create a database schema documentation including:
- All tables and their purpose
- Relationships between tables
- Key indexes
- Important constraints
- Sample data structure
```

### For Deployment:
```
Document the deployment process including:
- Prerequisites
- Step-by-step instructions
- Environment configuration
- Common issues and solutions
- Rollback procedure
```

---

## Tips for Best Results

1. **Be Specific:** Mention your project type, framework, and key features
2. **Provide Context:** Share what the project does and who uses it
3. **Ask for Explanations:** Request "why" explanations, not just "what"
4. **Request Examples:** Ask for code examples and use cases
5. **Iterate:** Review and ask for improvements
6. **Test:** Have someone new try to follow the Getting Started Guide

---

## Final Notes

- **Keep it updated:** Documentation should be a living document
- **Get feedback:** Have new developers test the Getting Started Guide
- **Version control:** Keep documentation in your repository
- **Regular reviews:** Update documentation when features change

---

**Good luck with your documentation!** 📚

